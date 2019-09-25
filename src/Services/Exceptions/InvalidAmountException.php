<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidAmountException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exception.invalid_amount_exception'), 13);
    }
}
