<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\DirectQuestionField;
use Bnb\PayboxGateway\QuestionTypeCode;
use Bnb\PayboxGateway\Responses\PayboxDirect\Refund as RefundResponse;

class Refund extends DirectRequest
{

    /**
     * Call number provided by Paybox Direct after capture request.
     *
     * @var string|null
     */
    protected $payboxCallNumber = null;

    /**
     * Transaction number provided by Paybox Direct after capture request.
     *
     * @var string|null
     */
    protected $payboxTransactionNumber = null;


    /**
     * Set call number provided by Paybox.
     *
     * @param string $payboxCallNumber
     *
     * @return $this
     */
    public function setPayboxCallNumber($payboxCallNumber)
    {
        $this->payboxCallNumber = $payboxCallNumber;

        return $this;
    }


    /**
     * Set transaction number provided by Paybox.
     *
     * @param string $payboxTransactionNumber
     *
     * @return $this
     */
    public function setPayboxTransactionNumber($payboxTransactionNumber)
    {
        $this->payboxTransactionNumber = $payboxTransactionNumber;

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
            DirectQuestionField::PAYBOX_CALL_NUMBER => $this->payboxCallNumber,
            DirectQuestionField::PAYBOX_TRANSACTION_NUMBER => $this->payboxTransactionNumber,
        ];
    }


    /**
     * @inheritdoc
     */
    public function getQuestionType()
    {
        return QuestionTypeCode::REFUND;
    }


    /**
     * @inheritdoc
     */
    public function getResponseClass()
    {
        return RefundResponse::class;
    }

}
