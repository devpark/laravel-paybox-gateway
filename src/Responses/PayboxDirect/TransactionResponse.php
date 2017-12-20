<?php
/**
 * laravel
 *
 * @author    Jérémy GAULIN <jeremy@bnb.re>
 * @copyright 2017 - B&B Web Expertise
 */

namespace Bnb\PayboxGateway\Responses\PayboxDirect;

use Bnb\PayboxGateway\DirectResponseField;

abstract class TransactionResponse extends Response
{

    public function getAuthorizationNumber()
    {
        return $this->getField(DirectResponseField::AUTHORIZATION_NUMBER);
    }


    public function getTransactionNumber()
    {
        return $this->getField(DirectResponseField::TRANSACTION_NUMBER);
    }


    public function getCallNumber()
    {
        return $this->getField(DirectResponseField::CALL_NUMBER);
    }


    public function geRemittanceNumber()
    {
        return $this->getField(DirectResponseField::REMITTANCE_NUMBER);
    }
}