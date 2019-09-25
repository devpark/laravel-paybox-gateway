<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCardNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_card_number_exception'));
    }
}
