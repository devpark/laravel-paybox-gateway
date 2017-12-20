<?php

namespace Bnb\PayboxGateway;

/**
 * Class QuestionTypeCode.
 *
 * It contains question type codes from Paybox Direct
 */
class QuestionTypeCode
{

    const AUTHORIZATION_ONLY = '00001';
    const CAPTURE_ONLY = '00002';
    const AUTHORIZATION_WITH_CAPTURE = '00003';
    const CREDIT = '00004';
    const CANCEL = '00005';
    const VERIFY = '00011';
    const TRANSACTION_WITHOUT_AUTHORIZATION = '00012';
    const MODIFY_TRANSACTION_AMOUNT = '00013';
    const REFUND = '00014';
    const CONSULT = '00017';
    const MIF_BRAND = '00018';
    const SUBSCRIBER_AUTHORIZATION_ONLY = '00051';
    const SUBSCRIBER_CAPTURE_ONLY = '00052';
    const SUBSCRIBER_AUTHORIZATION_WITH_CAPTURE = '00053';
    const SUBSCRIBER_CREDIT = '00054';
    const SUBSCRIBER_CANCEL = '00055';
    const SUBSCRIBER_REGISTER = '00056';
    const SUBSCRIBER_MODIFY = '00057';
    const SUBSCRIBER_DELETE = '00058';
    const TRANSACTION_WITHOUT_AUTHORIZATION_FORCED = '00061';
}
