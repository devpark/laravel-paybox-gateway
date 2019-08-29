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
    const _3D_SECURE_ID3D = 'ID3D';
    const _3D_SECURE_3DCAVV = '3DCAVV';
    const _3D_SECURE_3DCAVVALGO = '3DCAVVALGO';
    const _3D_SECURE_3DECI = '3DECI';
    const _3D_SECURE_3DENROLLED = '3DENROLLED';
    const _3D_SECURE_3DERROR = '3DERROR';
    const _3D_SECURE_3DSIGNVAL = '3DSIGNVAL';
    const _3D_SECURE_3DSTATUS = '3DSTATUS';
    const _3D_SECURE_3DXID = '3DXID';
}
