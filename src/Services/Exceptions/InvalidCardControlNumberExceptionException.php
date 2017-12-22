<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCardControlNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The provided card control number is not valid.');
    }
}