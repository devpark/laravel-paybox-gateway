<?php

namespace Bnb\PayboxGateway\Requests\Paybox;

class AuthorizationWithoutCapture extends Authorization
{
    /**
     * {@inheritdoc}
     */
    public function getBasicParameters()
    {
        $parameters = parent::getBasicParameters();
        $parameters['PBX_AUTOSEULE'] = 'O';

        return $parameters;
    }
}
