<?php

namespace Devpark\PayboxGateway\Services;

class SignatureVerifier
{
    public function __construct()
    {
    }

    public function isCorrect($signature, array $parameters)
    {
        return true;
    }
}
