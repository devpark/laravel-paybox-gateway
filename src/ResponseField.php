<?php

namespace Devpark\PayboxGateway;

class ResponseField
{
    const AMOUNT = 'M';
    const ORDER_NUMBER = 'R';
    const PAYBOX_CALL_NUMBER = 'T';
    const AUTHORIZATION_NUMBER = 'A';
    const SUBSCRIBER_NUMBER = 'B';
    const CARD_TYPE = 'C';
    const CARD_EXPIRATION_DATE = 'D';
    const RESPONSE_CODE = 'E';
    const SECURE_3D_AUTHENTICATION_STATUS = 'F';
    const SECURE_3D_GUARANTEE_PAYMENT = 'G';
    const CARD_HASH = 'H';
    const CARDHOLDER_COUNTRY_CODE_IP = 'I';
    const CARD_PAN_LAST_DIGITS = 'J';
    const SIGNATURE = 'K';
    const CARD_PAN_FIRST_DIGITS = 'N';
    const CARD_ENROLMENT = 'O';
    const PAYMENT_CASH_OR_CREDIT = 'o';
    const PAYMENT_TYPE = 'P';
    const TRANSACTION_TIMESTAMP = 'Q';
    const TRANSACTION_NUMBER = 'S';
    const SUBSCRIPTION_CARD_OR_PAYPAL_AUTHORIZATION = 'U';
    const TRANSACTION_PROCESSING_DATE = 'W';
    const BANK_COUNTRY_CODE = 'Y';
    const MIX_PAYMENT_INDEX = 'Z';
}
