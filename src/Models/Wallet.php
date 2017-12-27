<?php

namespace Bnb\PayboxGateway\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wallet
 *
 * @property int    id
 * @property string paybox_id
 * @property string subscriber_id
 * @property string card_number
 * @property Carbon card_expiration_date
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @package Bnb\PayboxGateway\Models
 */
class Wallet extends Model
{

    protected $table = 'ppps_wallets';

    protected $dates = ['card_expiration_date'];

    protected $fillable = [
        'paybox_id',
        'subscriber_id',
        'card_number',
        'card_expiration_date',
    ];


    /**
     * @param string $value
     */
    public function setCardNumberAttribute($value)
    {
        $this->attributes['card_number'] = Question::maskCardNumber($value);
    }


    /**
     * @return string
     */
    public function getPayboxSubscriberNumber()
    {
        return sprintf('WALLET_%1$010d', $this->id);
    }


    /**
     * @return bool
     */
    public function hasExpired()
    {
        return $this->card_expiration_date->lte(Carbon::now());
    }
}