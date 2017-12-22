<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class OperationFailedException extends \Exception
{

    private $errorCode;

    private $comment;


    public function __construct($errorCode, $comment)
    {
        parent::__construct(sprintf('The payment has failed : (%s) %s', $errorCode, $comment));

        $this->code = intval($errorCode);
        $this->errorCode = $errorCode;
        $this->comment = $comment;
    }


    public function getErrorCode()
    {
        return $this->errorCode;
    }


    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }
}