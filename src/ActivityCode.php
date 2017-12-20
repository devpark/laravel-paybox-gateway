<?php

namespace Bnb\PayboxGateway;

/**
 * Class DirectResponseCode.
 *
 * It contains response codes from Paybox Direct
 */
class ActivityCode
{
    const UNSPECIFIED = '020';
    const PHONE_REQUEST = '021';
    const LETTER_REQUEST = '022';
    const INTERNET_REQUEST = '023';
    const RECURRING_PAYMENT = '024';
}
