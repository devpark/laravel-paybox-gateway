<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class WalletHasExpiredException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The wallet has expired.');
    }
}