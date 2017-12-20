<?php

namespace Bnb\PayboxGateway;

/**
 * Class ResponseCode.
 * 
 * It contains response codes from Paybox System
 */
class ResponseCode
{
    const SUCCESS = '00000';
    const CONNECTION_FAILED = '00001';

    /**
     * This is not exact response code.
     */
    const PAYMENT_REJECTED = '001xx';

    const PAYBOX_ERROR = '00003';
    const INVALID_CARD_NUMBER = '00004';
    const REFUSED = '00006';
    const INVALID_EXPIRATION_DATE = '00008';
    const SUBSCRIBER_CREATION_ERROR = '00009';
    const INVALID_CURRENCY = '00010';
    const AMOUNT_INCORRECT = '00011';
    const PAYMENT_ALREADY_DONE = '00015';
    const SUBSCRIBER_ALREADY_EXISTS = '00016';
    const NOT_AUHORIZED_BIN_CARD = '00021';
    const OTHER_CARD_USED = '00029';
    const TIMEOUT = '00030';
    const RESERVED = '00031';
    const RESERVED_2 = '00032';
    const UNAUTHORIZED_COUNTRY_CODE = '00033';
    const BLOCKED_BY_FRAUD_FILTER = '00040';
    const WAITING_CONFIRMATION = '99999';
}
