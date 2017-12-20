<?php

return [
    /*
     * Whether test environment is enabled
     */
    'test' => env('PAYBOX_TEST', false),

    /*
     * Site number (provided by Paybox)
     */
    'site' => env('PAYBOX_SITE', ''),

    /*
     * Rank number (provided by Paybox)
     */
    'rank' => env('PAYBOX_RANK', ''),

    /*
     * Internal identifier (provided by Paybox)
     */
    'id' => env('PAYBOX_ID', ''),

    /*
     * HMAC authentication key - it should be generated in Paybox merchant panel
     */
    'hmac_key' => env('PAYBOX_HMAC_KEY', ''),

    /*
     * Paybox public key location - you can get it from 
     * http://www1.paybox.com/wp-content/uploads/2014/03/pubkey.pem
     */
    'public_key' => storage_path('paybox/pubkey.pem'),

    /*
     * Default return fields when going back from Paybox. You can change here keys as you want,
     * you can add also more values from ResponseField class     
     */
    'return_fields' => [
        'amount' => \Bnb\PayboxGateway\ResponseField::AMOUNT,
        'authorization_number' => \Bnb\PayboxGateway\ResponseField::AUTHORIZATION_NUMBER,
        'order_number' => \Bnb\PayboxGateway\ResponseField::ORDER_NUMBER,
        'response_code' => \Bnb\PayboxGateway\ResponseField::RESPONSE_CODE,
        'payment_type' => \Bnb\PayboxGateway\ResponseField::PAYMENT_TYPE,
        'call_number' => \Bnb\PayboxGateway\ResponseField::PAYBOX_CALL_NUMBER,
        'transaction_number' => \Bnb\PayboxGateway\ResponseField::TRANSACTION_NUMBER,
        // signature should be always last return field
        'signature' => \Bnb\PayboxGateway\ResponseField::SIGNATURE,
    ],

    /*
     * Those are routes names (not urls) where customer will be redirected after payment. If you 
     * want to use custom route with params in url you should set them dynamically when creating
     * authorization data. You shouldn't change keys here. Those urls will be later launched using 
     * GET HTTP request
     */
    'customer_return_routes_names' => [
        'accepted' => 'paybox.accepted',
        'refused' => 'paybox.refused',
        'aborted' => 'paybox.aborted',
        'waiting' => 'paybox.waiting',
    ],

    /*
     * This is route name (not url) where Paybox will send transaction status. This url is
     * independent from customer urls and it's the only url that should be used to track current
     * payment status for real. If you want to use custom route with params in url you should set it
     * dynamically when creating authorization data. This url will be later launched using GET HTTP
     * request
     */
    'transaction_verify_route_name' => 'paybox.process',

    /*
     * Access urls for Paybox for production environment
     */
    'production_urls' => [
        /*
         * Paybox System urls
         */
        'paybox' => [
            'https://tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
            'https://tpeweb1.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
        ],

        /*
         * Paybox Direct urls
         */
        'paybox_direct' => [
            'https://ppps.e-transactions.fr/PPPS.php',
            'https://ppps1.e-transactions.fr/PPPS.php',
        ],
    ],

    /*
     * Access urls for Paybox for test environment
     */
    'test_urls' => [
        /*
         * Paybox System urls
         */
        'paybox' => [
            'https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
        ],

        /*
         * Paybox Direct urls
         */
        'paybox_direct' => [
            'https://preprod-ppps.e-transactions.fr/PPPS.php',
        ],
    ],
];
