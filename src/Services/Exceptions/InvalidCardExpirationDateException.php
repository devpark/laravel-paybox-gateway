<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidCardExpirationDateException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_card_expiration_date_exception'), 116);
    }
}
