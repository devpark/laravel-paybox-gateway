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
    'hmac_key' => storage_path('app/private/hmac_key'),

    /*
     * Access urls for Paybox for production environment
     */
    'production_urls' => [
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
    'test_urls' => [
        /*
         * Paybox System urls
         */
        'paybox' => [
            'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi',
        ],
    ],
];
