<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

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
}
