<?php

namespace Devpark\PayboxGateway\Responses;

use Devpark\PayboxGateway\ResponseCode;
use Devpark\PayboxGateway\ResponseField;
use Devpark\PayboxGateway\Responses\Exceptions\InvalidSignature;
use Devpark\PayboxGateway\Services\Amount;
use Devpark\PayboxGateway\Services\SignatureVerifier;
use Exception;
use Illuminate\Http\Request;

class Verify
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Default parameters mapping from request.
     *
     * @var array
     */
    protected $parameters = [
        ResponseField::AMOUNT => 'amount',
        ResponseField::AUTHORIZATION_NUMBER => 'authorization_number',
        ResponseField::RESPONSE_CODE => 'response_code',
        ResponseField::SIGNATURE => 'signature',
    ];

    /**
     * @var SignatureVerifier
     */
    protected $signatureVerifier;

    /**
     * @var Amount
     */
    protected $amountService;

    /**
     * Verify constructor.
     *
     * @param Request $request
     * @param SignatureVerifier $signatureVerifier
     * @param Amount $amountService
     */
    public function __construct(
        Request $request,
        SignatureVerifier $signatureVerifier,
        Amount $amountService
    ) {
        $this->request = $request;
        $this->signatureVerifier = $signatureVerifier;
        $this->amountService = $amountService;
    }

    /**
     * Verify whether payment is valid and accepted.
     *
     * @param float $amount
     *
     * @return bool
     */
    public function isAccepted($amount)
    {
        $this->checkSignature();

        return $this->request->input($this->parameters[ResponseField::AUTHORIZATION_NUMBER]) &&
        $this->request->input($this->parameters[ResponseField::RESPONSE_CODE]) ==
        ResponseCode::SUCCESS &&
        $this->request->input($this->parameters[ResponseField::AMOUNT]) ==
        $this->amountService->get($amount, false);
    }

    /**
     * Set parameters map in order to make it possible to verify request in case custom request
     * parameters names vere used.
     *
     * @param array $parameters
     *
     * @throws Exception
     */
    public function setParametersMap(array $parameters)
    {
        if (! isset($parameters[ResponseField::AMOUNT])) {
            throw new Exception('Amount is missing');
        }

        if (! isset($parameters[ResponseField::AUTHORIZATION_NUMBER])) {
            throw new Exception('Authorization number is missing');
        }

        if (! isset($parameters[ResponseField::RESPONSE_CODE])) {
            throw new Exception('Response code is missing');
        }

        if (! isset($parameters[ResponseField::SIGNATURE])) {
            throw new Exception('Signature is missing');
        }

        $this->parameters = $parameters;
    }

    /**
     * @throws InvalidSignature
     */
    protected function checkSignature()
    {
        $signatureParameter = $this->parameters[ResponseField::SIGNATURE];

        if (! $this->signatureVerifier->isCorrect($signatureParameter,
            $this->request->except($signatureParameter))
        ) {
            throw new InvalidSignature();
        }
    }
}
