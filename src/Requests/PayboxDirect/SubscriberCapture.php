<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\DirectQuestionField;
use Bnb\PayboxGateway\QuestionTypeCode;
use Bnb\PayboxGateway\Responses\PayboxDirect\SubscriberCapture as SubscriberCaptureResponse;

class SubscriberCapture extends SubscriberRequest
{

    /**
     * Call number provided by Paybox Direct after authorization request.
     *
     * @var string|null
     */
    protected $payboxCallNumber = null;

    /**
     * Transaction number provided by Paybox Direct after authorization request.
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
     * @inheritdoc
     */
    public function getBasicParameters()
    {
        return [
            DirectQuestionField::AMOUNT => $this->amount,
            DirectQuestionField::CURRENCY => $this->currencyCode,
            DirectQuestionField::REFERENCE => $this->paymentNumber,
            DirectQuestionField::PAYBOX_CALL_NUMBER => $this->payboxCallNumber,
            DirectQuestionField::PAYBOX_TRANSACTION_NUMBER => $this->payboxTransactionNumber,
            DirectQuestionField::SUBSCRIBER_NUMBER => $this->subscriberNumber,
        ];
    }


    /**
     * @inheritdoc
     */
    public function getQuestionType()
    {
        return QuestionTypeCode::SUBSCRIBER_CAPTURE_ONLY;
    }


    /**
     * @inheritdoc
     */
    public function getResponseClass()
    {
        return SubscriberCaptureResponse::class;
    }
}
