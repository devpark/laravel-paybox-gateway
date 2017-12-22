<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidTransactionNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The transaction number is not valid.');
    }
}