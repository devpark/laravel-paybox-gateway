<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\ActivityCode;
use Bnb\PayboxGateway\DirectQuestionField;
use Bnb\PayboxGateway\QuestionTypeCode;
use Bnb\PayboxGateway\Responses\PayboxDirect\Authorization as AuthorizationResponse;
use Carbon\Carbon;

class Authorization extends DirectRequest
{

    /**
     * @var string
     */
    protected $cardNumber = null;

    /**
     * @var string
     */
    protected $cardExpirationDate = null;

    /**
     * @var string
     */
    protected $cardControlNumber = null;

    /**
     * @var string
     */
    protected $activity = ActivityCode::UNSPECIFIED;


    /**
     * Set card number provided by the customer.
     *
     * @param string $cardNumber
     *
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }


    /**
     * Set card expiration date provided by the customer.
     *
     * @param Carbon $cardExpirationDate
     *
     * @return $this
     */
    public function setCardExpirationDate(Carbon $cardExpirationDate)
    {
        $this->cardExpirationDate = $cardExpirationDate->format('dy');

        return $this;
    }


    /**
     * Set card control number (CVV) provided by the customer.
     *
     * @param string $cardControlNumber
     *
     * @return $this
     */
    public function setCardControlNumber($cardControlNumber)
    {
        $this->cardControlNumber = $cardControlNumber;

        return $this;
    }


    /**
     * Set operation activity code
     *
     * @param string $activity
     *
     * @return $this
     *
     * @see ActivityCode
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;

        return $this;
    }


    /**
     * Get parameters that will be send to Paybox Direct.
     *
     * @return array
     */
    public function getBasicParameters()
    {
        return [
            DirectQuestionField::AMOUNT => $this->amount,
            DirectQuestionField::CURRENCY => $this->currencyCode,
            DirectQuestionField::REFERENCE => $this->paymentNumber,
            DirectQuestionField::CARD_OR_WALLET_NUMBER => $this->cardNumber,
            DirectQuestionField::CARD_EXPIRATION_DATE => $this->cardExpirationDate,
            DirectQuestionField::CARD_CONTROL_NUMBER => $this->cardControlNumber,
            DirectQuestionField::ACTIVITY => $this->activity,
        ];
    }


    /**
     * @inheritdoc
     */
    public function getQuestionType()
    {
        return QuestionTypeCode::AUTHORIZATION_ONLY;
    }


    /**
     * @inheritdoc
     */
    public function getResponseClass()
    {
        return AuthorizationResponse::class;
    }

}
