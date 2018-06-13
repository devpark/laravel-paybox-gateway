<?php

namespace Bnb\PayboxGateway\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 *
 * @property int    id
 * @property string numquestion
 * @property string reference
 * @property array  data
 * @property string status
 * @property int    tries
 * @property string return_code
 * @property string return_content
 * @property Carbon notified_at
 *
 * @package Bnb\PayboxGateway\Models
 */
class Notification extends Model
{

    const MAX_RETRY_COUNT = 3;
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_DONE = 'done';

    protected $table = 'ppps_notifications';

    protected $dates = ['notified_at'];

    protected $casts = [
        'data' => 'array',
    ];

    protected $fillable = [
        'numquestion',
        'reference',
        'data',
        'status',
        'tries',
        'return_code',
        'return_content',
        'notified_at',
    ];


    public static function createFromResponse(Response $response, $reference, $amount)
    {
        return self::create([
            'numquestion' => $response->numquestion,
            'reference' => $reference,
            'data' => [
                'amount' => $amount,
                'transaction_number' => $response->numtrans,
                'call_number' => $response->numappel,
                'remittance_number' => $response->remise,
            ],
            'status' => self::STATUS_PENDING,
            'tries' => 0,
        ]);
    }

}