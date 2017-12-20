<?php

namespace Bnb\PayboxGateway;

/**
 * Class DirectResponseField.
 *
 * It contains response fields from Paybox Direct
 */
class DirectResponseField
{

    const RESPONSE_CODE = 'CODEREPONSE';
    const AUTHORIZATION_NUMBER = 'AUTORISATION';
    const COMMENT = 'COMMENTAIRE';
    const QUESTION_NUMBER = 'NUMQUESTION';
    const CALL_NUMBER = 'NUMAPPEL';
    const TRANSACTION_NUMBER = 'NUMTRANS';
    const CARD_COUNTRY = 'PAYS';
    const REMITTANCE_NUMBER = 'REMISE';
    const CARD_HASH = 'SHA-1';
    const CARD_TYPE = 'TYPECARTE';
    const SUBSCRIBER_REFERENCE = 'REFABONNE';
    const SUBSCRIBER_WALLET = 'PORTEUR';
    const STATUS = 'STATUS';
    const SITE = 'SITE';
    const RANK = 'RANG';
}
