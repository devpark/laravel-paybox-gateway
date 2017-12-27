<?php

namespace Bnb\PayboxGateway\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Question
 *
 * @property int    id
 * @property string numquestion
 * @property string version
 * @property string type
 * @property string site
 * @property string rang
 * @property string activite
 * @property string dateq
 * @property string reference
 * @property string refabonne
 * @property string montant
 * @property string devise
 * @property string porteur
 * @property string dateval
 * @property string cvv
 * @property string numappel
 * @property string numtrans
 * @property string hash
 * @property int    wallet_id
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @package   Bnb\PayboxGateway\Models
 */
class Question extends Model
{

    protected $table = 'ppps_questions';

    protected $fillable = [
        'version',
        'type',
        'site',
        'rang',
        'activite',
        'dateq',
        'reference',
        'refabonne',
        'montant',
        'devise',
        'porteur',
        'dateval',
        'cvv',
        'numappel',
        'numtrans',
        'hash',
        'wallet_id',
    ];


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::created(function (Question $question) {
            $question->numquestion = str_pad($question->id % 2147483647, 10, '0', STR_PAD_LEFT);
            $question->save();
        });
    }


    /**
     * @param string $value
     *
     * @return string
     */
    private static function maskCardControlNumber($value)
    {
        return preg_replace('/./', 'X', $value);
    }


    /**
     * @param string $value
     *
     * @return string
     */
    public static function maskCardNumber($value)
    {
        return preg_match('/^[0-9]{16}$/', $value) ?
            (substr($value, 0, 4) . str_repeat('X', 8) . substr($value, -4))
            : $value;
    }


    /**
     * @param string $value
     */
    public function setPorteurAttribute($value)
    {
        $this->attributes['porteur'] = self::maskCardNumber($value);
    }


    /**
     * @param string $value
     */
    public function setCvvAttribute($value)
    {
        $this->attributes['cvv'] = self::maskCardControlNumber($value);
    }


    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_only(parent::toArray(), [
            'numquestion',
            'version',
            'type',
            'site',
            'rang',
            'activite',
            'dateq',
            'reference',
            'refabonne',
            'montant',
            'devise',
            'porteur',
            'dateval',
            'cvv',
            'numappel',
            'numtrans',
            'hash',
        ]);
    }
}