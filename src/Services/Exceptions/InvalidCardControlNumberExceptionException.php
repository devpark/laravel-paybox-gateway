<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCardControlNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_card_control_number_exception'));
    }
}
