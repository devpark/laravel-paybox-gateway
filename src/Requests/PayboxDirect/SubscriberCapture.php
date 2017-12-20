<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

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
            'MONTANT' => $this->amount,
            'DEVISE' => $this->currencyCode,
            'REFERENCE' => $this->paymentNumber,
            'NUMAPPEL' => $this->payboxCallNumber,
            'NUMTRANS' => $this->payboxTransactionNumber,
            'REFABONNE' => $this->subscriberNumber,
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
