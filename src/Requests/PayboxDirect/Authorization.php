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

    /** @var array */
    protected $_3DSecure = [];


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
        $this->cardExpirationDate = $cardExpirationDate->format('my');

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
     * @param string $sID3D
     * @param string $s3DCAVV
     * @param string $s3DCAVVALGO
     * @param string $s3DECI
     * @param string $s3DENROLLED
     * @param string $s3DERROR
     * @param string $s3DSIGNVAL
     * @param string $s3DSTATUS
     * @param string $s3DXID
     */
    public function set3DSecure(
        $sID3D,
        $s3DCAVV = null,
        $s3DCAVVALGO = null,
        $s3DECI = null,
        $s3DENROLLED = null,
        $s3DERROR = null,
        $s3DSIGNVAL = null,
        $s3DSTATUS = null,
        $s3DXID = null
    ) {
        $this->_3DSecure['ID3D'] = $sID3D;
        $this->_3DSecure['3DCAVV'] = $s3DCAVV;
        $this->_3DSecure['3DCAVVALGO'] = $s3DCAVVALGO;
        $this->_3DSecure['3DECI'] = $s3DECI;
        $this->_3DSecure['3DENROLLED'] = $s3DENROLLED;
        $this->_3DSecure['3DERROR'] = $s3DERROR;
        $this->_3DSecure['3DSIGNVAL'] = $s3DSIGNVAL;
        $this->_3DSecure['3DSTATUS'] = $s3DSTATUS;
        $this->_3DSecure['3DXID'] = $s3DXID;

        $this->_3DSecure = array_filter($this->_3DSecure);

        return $this;
    }


    /**
     * Get parameters that will be send to Paybox Direct.
     *
     * @return array
     */
    public function getBasicParameters()
    {
        $params = [
            DirectQuestionField::AMOUNT => $this->amount,
            DirectQuestionField::CURRENCY => $this->currencyCode,
            DirectQuestionField::REFERENCE => $this->paymentNumber,
            DirectQuestionField::CARD_OR_WALLET_NUMBER => $this->cardNumber,
            DirectQuestionField::CARD_EXPIRATION_DATE => $this->cardExpirationDate,
            DirectQuestionField::CARD_CONTROL_NUMBER => $this->cardControlNumber,
            DirectQuestionField::ACTIVITY => $this->activity,
        ];

        if ( ! empty($this->_3DSecure)) {
            $params = $params + array_filter([
                    DirectQuestionField::_3D_SECURE_ID3D => $this->_3DSecure['ID3D'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DCAVV => $this->_3DSecure['3DCAVV'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DCAVVALGO => $this->_3DSecure['3DCAVVALGO'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DECI => $this->_3DSecure['3DECI'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DENROLLED => $this->_3DSecure['3DENROLLED'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DERROR => $this->_3DSecure['3DERROR'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DSIGNVAL => $this->_3DSecure['3DSIGNVAL'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DSTATUS => $this->_3DSecure['3DSTATUS'] ?? null,
                    DirectQuestionField::_3D_SECURE_3DXID => $this->_3DSecure['3DXID'] ?? null,
                ]);
        }

        return $params;
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
