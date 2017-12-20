<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\QuestionTypeCode;
use Bnb\PayboxGateway\Responses\PayboxDirect\AuthorizationWithCapture as AuthorizationWithCaptureResponse;

class AuthorizationWithCapture extends Authorization
{

    /**
     * @inheritdoc
     */
    public function getQuestionType()
    {
        return QuestionTypeCode::AUTHORIZATION_WITH_CAPTURE;
    }


    /**
     * @inheritdoc
     */
    public function getResponseClass()
    {
        return AuthorizationWithCaptureResponse::class;
    }
}
