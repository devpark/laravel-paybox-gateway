<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCardNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The provided card number is not valid.');
    }
}