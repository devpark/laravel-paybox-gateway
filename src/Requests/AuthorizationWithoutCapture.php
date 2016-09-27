<?php

namespace Devpark\PayboxGateway\Requests;

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
