<?php

namespace Devpark\PayboxGateway;

/**
 * Class DirectResponseCode.
 *
 * It contains response codes from Paybox Direct
 */
class DirectResponseCode
{
    const SUCCESS = '00000';
    const CONNECTION_FAILED = '00001';

    /**
     * This is not exact response code.
     */
    const PAYMENT_REJECTED = '001xx';

    const INCOHERENCE_ERROR = '00002';
    const PAYBOX_ERROR = '00003';
    const INVALID_CARD_NUMBER = '00004';
    const INVALID_REQUEST_NUMBER = '00005';
    const REFUSED = '00006';
    const INVALID_DATE = '00007';
    const INVALID_EXPIRATION_DATE = '00008';
    const INVALID_OPERATION_TYPE = '00009';
    const INVALID_CURRENCY = '00010';
    const AMOUNT_INCORRECT = '00011';
    const INVALID_ORDER_REFERENCE = '00012';
    const VERSION_NOT_SUPPORTED = '00013';
    const REQUEST_INCOHERANT = '00014';
    const ERROR_ACCESSING_PREVIOUSLY = '00015';
    const SUBSCRIBER_ALREADY_EXISTS = '00016';
    const SUBSCRIBER_NOT_EXISTS = '00017';
    const TRANSACTION_NOT_FOUND = '00018';
    const RESERVED = '00019';
    const VISUAL_CRYPTOGRAM_MISSING = '00020';
    const NOT_AUHORIZED_BIN_CARD = '00021';
    const THREESHOLD_REACHED = '00022';
    const CARHOLDER_ALREADY_SEEN = '00023';
    const COUNTRY_CODE_FILTERED = '00024';
    const CARHOLDER_ENLORED_BUT_NOT_AUTHENTICATED = '00040';
    const TIMEOUT = '00097';
    const INTERNAL_TIMEOUT = '00098';
    const QUERY_REPLY_INCOHERENCE = '00099';
}
