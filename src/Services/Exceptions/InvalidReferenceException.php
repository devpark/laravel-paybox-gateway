<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidReferenceException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The provided reference is not valid.');
    }
}