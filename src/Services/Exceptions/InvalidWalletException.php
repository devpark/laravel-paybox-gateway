<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidWalletException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The wallet is not valid.');
    }
}