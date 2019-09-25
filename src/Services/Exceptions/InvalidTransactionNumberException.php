<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidTransactionNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_transaction_number_exception'));
    }
}
