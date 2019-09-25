<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidWalletException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_wallet_exception'));
    }
}
