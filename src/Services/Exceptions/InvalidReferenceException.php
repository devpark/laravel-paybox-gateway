<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class InvalidReferenceException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.invalid_reference_exception'));
    }
}
