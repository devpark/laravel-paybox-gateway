<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCallNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The call number is not valid.');
    }
}