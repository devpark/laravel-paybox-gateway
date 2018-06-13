<?php

namespace Bnb\PayboxGateway\Jobs;

use Bnb\PayboxGateway\HttpClient\GuzzleHttpClient;
use Bnb\PayboxGateway\Models\Notification;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Log;
use Mail;

class NotifyPaymentStatus implements ShouldQueue
{

    use Queueable, InteractsWithQueue;

    /**
     * @var int
     */
    private $notificationId;


    public function __construct(Notification $notification)
    {
        $queue = config('paybox.notifications.queue');
        $this->onConnection($queue['connection']);
        $this->onQueue($queue['queue']);
        $this->notificationId = $notification->id;
    }


    public function handle(GuzzleHttpClient $client)
    {
        /** @var Notification $notification */
        $notification = Notification::query()->find($this->notificationId);

        if ( ! $notification
            || $notification->status !== Notification::STATUS_PENDING
            || $notification->tries >= Notification::MAX_RETRY_COUNT) {
            return;
        }

        $notifyUrl = config('paybox.notifications.url');
        try {
            $hash = md5(join('+', [
                'V1',
                $notification->reference,
                $notification->data['transaction_number'],
                $notification->data['call_number'],
                $notification->data['remittance_number'],
                $notification->data['amount']
            ]));
            $response = $client->requestRaw($notifyUrl, ['hash' => $hash, 'reference' => $notification->reference] + $notification->data);
            if ($response->getStatusCode() !== 200 || ! empty(trim((string)$response->getBody()))) {
                $this->fail($notification, $response->getStatusCode(), $response->getBody());
            } else {
                $this->succeeded($notification);
            }
        } catch (GuzzleException $e) {
            Log::error(sprintf('Failed IPN notifications : %s', $e->getMessage()), [
                'id' => $notification->id,
                'question' => $notification->numquestion,
                'reference' => $notification->reference,
                'trace' => $e->getTraceAsString()
            ]);
            $this->fail($notification, $e->getCode(), $e->getMessage());
        }
    }


    private function fail(Notification $notification, $code, $body)
    {
        $notification->tries += 1;
        $notification->return_code = $code;
        $notification->return_content = $body;

        if ($email = config('paybox.notifications.notify_to')) {
            $notification->notified_at = Carbon::now();
        }

        if ( ! ($retry = ($notification->tries < Notification::MAX_RETRY_COUNT))) {
            $notification->status = Notification::STATUS_FAILED;
        }

        $notification->save();

        if ($email) {
            try {
                $data = [
                    'url' => config('paybox.notifications.url'),
                    'id' => $notification->id,
                    'reference' => $notification->reference,
                    'code' => $notification->return_code,
                    'content' => $notification->return_content,
                    'tries' => $notification->tries,
                ];
                Mail::raw(<<<EMAIL
IPN notification as failed #{$data['tries']}:

URL: {$data['url']}
ID: {$data['id']}
CODE: {$data['code']}
CONTENT:
{$data['content']}
EMAIL
                    , function (Message $message) use ($email, $data) {
                        $message
                            ->from(
                                config('paybox.notifications.notify_from.address', config('mail.from.address')),
                                config('paybox.notifications.notify_from.name', config('mail.from.name'))
                            )
                            ->to($email)
                            ->subject(sprintf('IPN notification failure %s (#%s)', $data['reference'], $data['tries']));
                    }
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send IPN failrure notification email');
                Log::error($e);
            }
        }

        if ($retry) {
            $this->release(max(15, $notification->tries * intval(config('paybox.notifications.retry_after'))));
        }
    }


    private function succeeded(Notification $notification)
    {
        $notification->tries += 1;
        $notification->status = Notification::STATUS_DONE;
        $notification->return_code = 200;
        $notification->return_content = null;
        $notification->notified_at = null;
        $notification->save();
    }
}