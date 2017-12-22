<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidAmountException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The amount is not valid.');
    }
}