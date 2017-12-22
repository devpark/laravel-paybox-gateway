<?php

namespace Bnb\PayboxGateway\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Response
 *
 * @property int    id
 * @property string numquestion
 * @property string site
 * @property string rang
 * @property string codereponse
 * @property string numappel
 * @property string numtrans
 * @property string autorisation
 * @property string remise
 * @property string typecarte
 * @property string pays
 * @property string porteur
 * @property string refabonne
 * @property string commentaire
 * @property string status
 * @property string sha
 * @property int    wallet_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Wallet wallet
 *
 * @package Bnb\PayboxGateway\Models
 */
class Response extends Model
{

    protected $table = 'ppps_responses';

    protected $fillable = [
        'numquestion',
        'site',
        'rang',
        'codereponse',
        'numappel',
        'numtrans',
        'autorisation',
        'remise',
        'typecarte',
        'pays',
        'porteur',
        'refabonne',
        'commentaire',
        'status',
        'sha',
        'wallet_id',
    ];


    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }
}