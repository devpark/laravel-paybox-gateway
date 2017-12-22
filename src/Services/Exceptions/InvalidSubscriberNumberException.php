<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidSubscriberNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct('The subscriber number is not valid.');
    }
}