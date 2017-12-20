<?php
/**
 * laravel
 *
 * @author    Jérémy GAULIN <jeremy@bnb.re>
 * @copyright 2017 - B&B Web Expertise
 */

namespace Bnb\PayboxGateway\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wallet
 *
 * @property int id
 * @property string paybox_id
 * @property string customer_id
 * @property string card_expiration_date
 * @property string card_number
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @package Bnb\PayboxGateway\Models
 */
class Wallet extends Model
{

    protected $table = 'ppps_wallets';


    /**
     * Relationship: model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }
}