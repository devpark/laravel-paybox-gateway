<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\Models\Wallet;

abstract class SubscriberRequest extends DirectRequest
{

    /**
     * @var string
     */
    protected $subscriberNumber = null;

    /**
     * @var string
     */
    protected $subscriberWallet = null;

    /**
     * @var int
     */
    protected $walletId = null;


    /**
     * Set the wallet
     *
     * @param Wallet $wallet
     *
     * @return $this
     */
    public function setWallet(Wallet $wallet)
    {
        $this->walletId = $wallet->id;
        $this->subscriberNumber = sprintf('%s%010d', config('paybox.wallet_prefix'), $wallet->id);
        $this->subscriberWallet = $wallet->paybox_id;

        return $this;
    }


    /**
     * Set the subscriber number
     *
     * @param string $subscriberNumber
     *
     * @return $this
     */
    public function setSubscriberNumber($subscriberNumber)
    {
        $this->subscriberNumber = $subscriberNumber;

        return $this;
    }


    /**
     * Set the subscriber wallet
     *
     * @param string $subscriberWallet
     *
     * @return $this
     */
    public function setSubscriberWallet($subscriberWallet)
    {
        $this->subscriberWallet = $subscriberWallet;

        return $this;
    }


    /**
     * @param array $params
     *
     * @return array
     */
    protected function buildQuestionAttributes(array $params)
    {
        $params = parent::buildQuestionAttributes($params);

        if ( ! empty($this->walletId)) {
            $params['wallet_id'] = $this->walletId;
        }

        return $params;
    }


    /**
     * @param array $params
     *
     * @return array
     */
    protected function buildResponseAttributes(array $params)
    {
        $params = parent::buildResponseAttributes($params);

        if ( ! empty($this->walletId)) {
            $params['wallet_id'] = $this->walletId;
        }

        return $params;
    }
}
