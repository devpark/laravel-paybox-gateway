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
     */
    public function setPorteurAttribute($value)
    {
        $this->attributes['porteur'] = preg_match('/^[0-9]{16}$/', $value) ? (str_repeat('X', 12) . substr($value, -4)) : $value;
    }


    /**
     * @param string $value
     */
    public function setCvvAttribute($value)
    {
        $this->attributes['cvv'] = preg_replace('/./', 'X', $value);
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