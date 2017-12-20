<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\QuestionTypeCode;
use Bnb\PayboxGateway\Responses\PayboxDirect\SubscriberAuthorizationWithCapture as SubscriberAuthorizationWithCaptureResponse;

class SubscriberAuthorizationWithCapture extends SubscriberAuthorization
{

    /**
     * @inheritdoc
     */
    public function getQuestionType()
    {
        return QuestionTypeCode::SUBSCRIBER_AUTHORIZATION_WITH_CAPTURE;
    }


    /**
     * @inheritdoc
     */
    public function getResponseClass()
    {
        return SubscriberAuthorizationWithCaptureResponse::class;
    }

}
