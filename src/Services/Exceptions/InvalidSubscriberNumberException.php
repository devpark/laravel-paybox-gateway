<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidSubscriberNumberException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_subscriber_number_exception'));
    }
}
