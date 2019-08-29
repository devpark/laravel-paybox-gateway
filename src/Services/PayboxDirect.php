<?php

namespace Bnb\PayboxGateway\Services;

use App;
use Bnb\PayboxGateway\ActivityCode;
use Bnb\PayboxGateway\Models\Response;
use Bnb\PayboxGateway\Models\Wallet;
use Bnb\PayboxGateway\Requests\PayboxDirect\AuthorizationWithCapture;
use Bnb\PayboxGateway\Requests\PayboxDirect\Refund;
use Bnb\PayboxGateway\Requests\PayboxDirect\SubscriberAuthorizationWithCapture;
use Bnb\PayboxGateway\Requests\PayboxDirect\SubscriberCapture;
use Bnb\PayboxGateway\Requests\PayboxDirect\SubscriberDelete;
use Bnb\PayboxGateway\Requests\PayboxDirect\SubscriberRegister;
use Bnb\PayboxGateway\Responses\PayboxDirect\AuthorizationWithCapture as AuthorizationWithCaptureResponse;
use Bnb\PayboxGateway\Responses\PayboxDirect\Refund as RefundResponse;
use Bnb\PayboxGateway\Responses\PayboxDirect\SubscriberAuthorizationWithCapture as SubscriberAuthorizationWithCaptureResponse;
use Bnb\PayboxGateway\Responses\PayboxDirect\SubscriberCapture as SubscriberCaptureResponse;
use Bnb\PayboxGateway\Responses\PayboxDirect\SubscriberDelete as SubscriberDeleteResponse;
use Bnb\PayboxGateway\Responses\PayboxDirect\SubscriberRegister as SubscriberRegisterResponse;
use Bnb\PayboxGateway\Services\Exceptions\InvalidAmountException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidCallNumberException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidCardControlNumberException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidCardExpirationDateException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidCardNumberException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidReferenceException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidSubscriberNumberException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidTransactionNumberException;
use Bnb\PayboxGateway\Services\Exceptions\InvalidWalletException;
use Bnb\PayboxGateway\Services\Exceptions\OperationFailedException;
use Bnb\PayboxGateway\Services\Exceptions\WalletHasExpiredException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class PayboxDirect
{

    /**
     * @param float  $amount
     * @param string $cardNumber
     * @param Carbon $cardExpirationDate
     * @param string $cardControlNumber
     * @param string $reference
     * @param string $activity
     * @param string $sID3D
     * @param string $s3DCAVV
     * @param string $s3DCAVVALGO
     * @param string $s3DECI
     * @param string $s3DENROLLED
     * @param string $s3DERROR
     * @param string $s3DSIGNVAL
     * @param string $s3DSTATUS
     * @param string $s3DXID
     *
     * @return Response
     * @throws InvalidAmountException
     * @throws InvalidCardControlNumberException
     * @throws InvalidCardExpirationDateException
     * @throws InvalidCardNumberException
     * @throws InvalidReferenceException
     * @throws OperationFailedException
     */
    public static function debit(
        $amount,
        $cardNumber,
        Carbon $cardExpirationDate,
        $cardControlNumber,
        $reference,
        $activity = ActivityCode::INTERNET_REQUEST,
        $sID3D = null,
        $s3DCAVV = null,
        $s3DCAVVALGO = null,
        $s3DECI = null,
        $s3DENROLLED = null,
        $s3DERROR = null,
        $s3DSIGNVAL = null,
        $s3DSTATUS = null,
        $s3DXID = null
    ) {

        $amount = self::validateAmount($amount);
        $cardNumber = self::validateCardNumber($cardNumber);
        $cardControlNumber = self::validateCardControlNumber($cardControlNumber);
        $cardExpirationDate = self::validateCardExpirationDate($cardExpirationDate);
        $reference = self::validateReference($reference);

        $request = App::make(AuthorizationWithCapture::class);
        /** @var AuthorizationWithCaptureResponse $response */
        $response = $request
            ->setAmount($amount)
            ->setCardNumber($cardNumber)
            ->setCardExpirationDate($cardExpirationDate)
            ->setCardControlNumber($cardControlNumber)
            ->setActivity($activity)
            ->setPaymentNumber($reference)
            ->set3DSecure($sID3D, $s3DCAVV, $s3DCAVVALGO, $s3DECI, $s3DENROLLED, $s3DERROR, $s3DSIGNVAL, $s3DSTATUS, $s3DXID)
            ->send()
        ;

        if ($response->isSuccess()) {
            return $response->getModel();
        }

        throw new OperationFailedException($response->getResponseCode(), $response->getComment());
    }


    /**
     * @param float  $amount
     * @param string $callNumber
     * @param string $transactionNumber
     *
     * @return Response
     * @throws InvalidAmountException
     * @throws OperationFailedException
     * @throws InvalidCallNumberException
     * @throws InvalidTransactionNumberException
     */
    public static function refund($amount, $callNumber, $transactionNumber)
    {
        $amount = self::validateAmount($amount);
        $callNumber = self::validateCallNumber($callNumber);
        $transactionNumber = self::validateTransactionNumber($transactionNumber);

        /** @var Refund $request */
        $request = App::make(Refund::class);
        /** @var RefundResponse $response */
        $response = $request
            ->setAmount($amount)
            ->setPayboxCallNumber($callNumber)
            ->setPayboxTransactionNumber($transactionNumber)
            ->send()
        ;

        if ($response->isSuccess()) {
            return $response->getModel();
        }

        throw new OperationFailedException($response->getResponseCode(), $response->getComment());
    }


    /**
     * @param float  $amount
     * @param string $subscriberNumber
     * @param string $cardNumber
     * @param Carbon $cardExpirationDate
     * @param string $cardControlNumber
     * @param string $reference
     * @param string $activity
     *
     * @return Response
     * @throws OperationFailedException
     * @throws InvalidSubscriberNumberException
     * @throws InvalidAmountException
     * @throws InvalidCardNumberException
     * @throws InvalidCardExpirationDateException
     * @throws InvalidCardControlNumberException
     * @throws InvalidReferenceException
     * @throws Exception
     */
    public static function createAndDebitWallet(
        $amount,
        $subscriberNumber,
        $cardNumber,
        Carbon $cardExpirationDate,
        $cardControlNumber,
        $reference,
        $activity = ActivityCode::INTERNET_REQUEST
    ) {
        $amount = self::validateAmount($amount);
        $subscriberNumber = self::validateSubscriberNumber($subscriberNumber);
        $cardNumber = self::validateCardNumber($cardNumber);
        $cardControlNumber = self::validateCardControlNumber($cardControlNumber);
        $cardExpirationDate = self::validateCardExpirationDate($cardExpirationDate);
        $reference = self::validateReference($reference);

        /** @var Wallet $wallet */
        $wallet = Wallet::create([
            'subscriber_id' => $subscriberNumber,
            'card_expiration_date' => $cardExpirationDate,
            'card_number' => $cardNumber,
        ]);

        try {
            /** @var SubscriberRegister $request */
            $request = App::make(SubscriberRegister::class);
            /** @var SubscriberRegisterResponse $response */
            $response = $request
                ->setAmount($amount)
                ->setWallet($wallet)
                ->setCardNumber($cardNumber)
                ->setCardExpirationDate($cardExpirationDate)
                ->setCardControlNumber($cardControlNumber)
                ->setActivity($activity)
                ->setPaymentNumber($reference)
                ->send()
            ;
        } catch (Exception $e) {
            if ( ! empty($wallet)) {
                try {
                    $wallet->delete();
                } catch (Exception $e) {
                    // NTD
                }
            }

            throw $e;
        }

        if ($response->isSuccess()) {
            try {
                $wallet->paybox_id = $response->getSubscriberWallet();
                $wallet->save();

                /** @var SubscriberCapture $request */
                $request = App::make(SubscriberCapture::class);
                /** @var SubscriberCaptureResponse $response */
                $response = $request
                    ->setAmount($amount)
                    ->setWallet($wallet)
                    ->setPaymentNumber($reference)
                    ->setPayboxTransactionNumber($response->getTransactionNumber())
                    ->setPayboxCallNumber($response->getCallNumber())
                    ->send()
                ;

                if ($response->isSuccess()) {
                    return $response->getModel();
                }
            } catch (Exception $e) {
                if ( ! empty($wallet)) {
                    try {
                        self::deleteWallet($wallet->id);
                    } catch (Throwable $e) {
                        throw new OperationFailedException($e->getCode(),
                            sprintf('Failed to delete wallet after capture failure: %s', $e->getMessage()));
                    }
                }

                throw new OperationFailedException($e->getCode(), sprintf('Failed to capture wallet: %s', $e->getMessage()));
            }
        }

        throw new OperationFailedException($response->getResponseCode(), $response->getComment());
    }


    /**
     * @param float  $amount
     * @param int    $wallet
     * @param string $cardControlNumber
     * @param string $reference
     * @param string $activity
     *
     * @return Response
     * @throws OperationFailedException
     * @throws InvalidAmountException
     * @throws InvalidCardControlNumberException
     * @throws InvalidReferenceException
     * @throws InvalidWalletException
     * @throws WalletHasExpiredException
     */
    public static function debitWallet(
        $amount,
        $wallet,
        $cardControlNumber,
        $reference,
        $activity = ActivityCode::INTERNET_REQUEST
    ) {
        $amount = self::validateAmount($amount);
        $cardControlNumber = self::validateCardControlNumber($cardControlNumber);
        $reference = self::validateReference($reference);

        /** @var Wallet $wallet */
        $wallet = Wallet::find($wallet);

        if (empty($wallet)) {
            throw new InvalidWalletException();
        }

        if ($wallet->hasExpired()) {
            throw new WalletHasExpiredException();
        }

        /** @var SubscriberAuthorizationWithCapture $request */
        $request = App::make(SubscriberAuthorizationWithCapture::class);
        /** @var SubscriberAuthorizationWithCaptureResponse $response */
        $response = $request
            ->setAmount($amount)
            ->setWallet($wallet)
            ->setCardExpirationDate($wallet->card_expiration_date)
            ->setCardControlNumber($cardControlNumber)
            ->setActivity($activity)
            ->setPaymentNumber($reference)
            ->send()
        ;

        if ($response->isSuccess()) {
            return $response->getModel();
        }

        throw new OperationFailedException($response->getResponseCode(), $response->getComment());
    }


    /**
     * @param int $wallet
     *
     * @return Response
     * @throws OperationFailedException
     * @throws InvalidWalletException
     */
    public static function deleteWallet($wallet)
    {
        /** @var Wallet $wallet */
        $wallet = Wallet::find($wallet);

        if (empty($wallet)) {
            throw new InvalidWalletException();
        }

        /** @var SubscriberDelete $request */
        $request = App::make(SubscriberDelete::class);
        /** @var SubscriberDeleteResponse $response */
        $response = $request
            ->setWallet($wallet)
            ->send()
        ;

        if ($response->isSuccess()) {
            try {
                $wallet->delete();
            } catch (Throwable $e) {
                // NTD
            }

            return $response->getModel();
        }

        throw new OperationFailedException($response->getResponseCode(), $response->getComment());
    }


    /**
     * @param $subscriberNumber
     *
     * @return Wallet[]|Collection
     * @throws InvalidSubscriberNumberException
     */
    public static function listWallets($subscriberNumber)
    {

        $subscriberNumber = self::validateSubscriberNumber($subscriberNumber);

        return Wallet::where('subscriber_id', '=', $subscriberNumber)->get();
    }


    /**
     * @param float $amount
     *
     * @return float
     * @throws InvalidAmountException
     */
    private static function validateAmount($amount)
    {
        $amount = ! is_float($amount) ? floatval($amount) : $amount;

        if ($amount < 0.00) {
            throw new InvalidAmountException();
        }

        return $amount;
    }


    /**
     * @param string $cardNumber
     *
     * @return null|string|string[]
     * @throws InvalidCardNumberException
     */
    private static function validateCardNumber($cardNumber)
    {
        $cardNumber = preg_replace('/[^0-9]+/', '', $cardNumber);

        if ( ! preg_match('/^[0-9]{16}$/', $cardNumber)) {
            throw new InvalidCardNumberException();
        }

        return $cardNumber;
    }


    /**
     * @param string $cardControlNumber
     *
     * @return string
     * @throws InvalidCardControlNumberException
     */
    private static function validateCardControlNumber($cardControlNumber)
    {
        $cardControlNumber = preg_replace('/[^0-9]+/', '', $cardControlNumber);

        if ( ! preg_match('/^[0-9]{3,4}$/', $cardControlNumber)) {
            throw new InvalidCardControlNumberException();
        }

        return $cardControlNumber;
    }


    /**
     * @param Carbon $cardExpirationDate
     *
     * @return Carbon
     * @throws InvalidCardExpirationDateException
     */
    private static function validateCardExpirationDate(Carbon $cardExpirationDate)
    {
        $cardExpirationDate = $cardExpirationDate->lastOfMonth()->endOfDay();

        if ($cardExpirationDate->lt(Carbon::now())) {
            throw new InvalidCardExpirationDateException();
        }

        return $cardExpirationDate;
    }


    /**
     * @param string $reference
     *
     * @return string
     * @throws InvalidReferenceException
     */
    private static function validateReference($reference)
    {
        $reference = trim($reference);

        if (empty($reference)) {
            throw new InvalidReferenceException();
        }

        return $reference;
    }


    /**
     * @param string $callNumber
     *
     * @return string
     * @throws InvalidCallNumberException
     */
    private static function validateCallNumber($callNumber)
    {
        $callNumber = preg_replace('/[^0-9]+/', '', $callNumber);

        if ( ! preg_match('/^[0-9]{10}$/', $callNumber)) {
            throw new InvalidCallNumberException();
        }

        return $callNumber;
    }


    /**
     * @param string $transactionNumber
     *
     * @return string
     * @throws InvalidTransactionNumberException
     */
    private static function validateTransactionNumber($transactionNumber)
    {
        $transactionNumber = preg_replace('/[^0-9]+/', '', $transactionNumber);

        if ( ! preg_match('/^[0-9]{10}$/', $transactionNumber)) {
            throw new InvalidTransactionNumberException();
        }

        return $transactionNumber;
    }


    /**
     * @param string $subscriberNumber
     *
     * @return string
     * @throws InvalidSubscriberNumberException
     */
    private static function validateSubscriberNumber($subscriberNumber)
    {
        $subscriberNumber = trim($subscriberNumber);

        if ( ! preg_match('/^.{1,191}$/', $subscriberNumber)) {
            throw new InvalidSubscriberNumberException();
        }

        return $subscriberNumber;
    }
}
