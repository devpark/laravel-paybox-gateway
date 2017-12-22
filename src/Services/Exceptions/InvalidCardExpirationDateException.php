<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCardExpirationDateException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The provided card expiration date is not valid.');
    }
}