<?php

namespace Bnb\PayboxGateway\Responses\PayboxDirect;

use Bnb\PayboxGateway\DirectResponseField;

abstract class SubscriberTransactionResponse extends TransactionResponse
{

    public function getSubscriberReference()
    {
        return $this->getField(DirectResponseField::SUBSCRIBER_REFERENCE);
    }


    public function getSubscriberWallet()
    {
        return $this->getField(DirectResponseField::SUBSCRIBER_WALLET);
    }

}