<?php

namespace Bnb\PayboxGateway;

/**
 * Class DirectResponseField.
 *
 * It contains response fields from Paybox Direct
 */
class DirectQuestionField
{

    const HASH = 'HASH';
    const PAYBOX_VERSION = 'VERSION';
    const PAYBOX_TYPE = 'TYPE';
    const PAYBOX_SITE = 'SITE';
    const PAYBOX_RANK = 'RANG';
    const PAYBOX_QUESTION_NUMBER = 'NUMQUESTION';
    const PAYBOX_QUESTION_DATE = 'DATEQ';
    const AMOUNT = 'MONTANT';
    const CURRENCY = 'DEVISE';
    const REFERENCE = 'REFERENCE';
    const CARD_OR_WALLET_NUMBER = 'PORTEUR';
    const CARD_EXPIRATION_DATE = 'DATEVAL';
    const CARD_CONTROL_NUMBER = 'CVV';
    const ACTIVITY = 'ACTIVITE';
    const PAYBOX_CALL_NUMBER = 'NUMAPPEL';
    const PAYBOX_TRANSACTION_NUMBER = 'NUMTRANS';
    const SUBSCRIBER_NUMBER = 'REFABONNE';
    const HMAC = 'HMAC';
}
