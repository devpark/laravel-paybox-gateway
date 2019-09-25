<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class WalletHasExpiredException extends \Exception
{

    public function __construct()
    {
        parent::__construct(trans('paybox::exceptions.wallet_has_expired_exception'), 99);
    }
}
