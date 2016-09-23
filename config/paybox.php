<?php

return [
    /*
     * Whether test environment is enabled
     */
    'test' => env('PAYBOX_TEST', false),

    /*
     * Access urls for Paybox for production environment
     */
    'production_servers' => [
        /*
         * Paybox System urls
         */
        'paybox' => [
            'https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi',
            'https://tpeweb1.paybox.com/cgi/MYchoix_pagepaiement.cgi',
        ],
    ],

    /*
     * Access urls for Paybox for test environment
     */    
    'test_servers' => [
        /*
         * Paybox System urls
         */
        'paybox' => [
            'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi',
        ],
    ],
];
