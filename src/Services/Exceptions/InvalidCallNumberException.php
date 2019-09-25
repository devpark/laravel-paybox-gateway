<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCallNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_call_number_exception'), 99);
    }
}
