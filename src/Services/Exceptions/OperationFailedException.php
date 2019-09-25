<?php

namespace Bnb\PayboxGateway\Services\Exceptions;

class OperationFailedException extends \Exception
{

    private $errorCode;

    private $comment;


    public function __construct($errorCode, $comment)
    {
        parent::__construct(trans('paybox::exceptions.operation_failed_exception', ['code' => $errorCode, 'message' => $comment]));

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
