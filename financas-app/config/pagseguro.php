<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PagSeguro Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your PagSeguro integration settings.
    | You can get these credentials from your PagSeguro account.
    |
    */

    'email' => env('PAGSEGURO_EMAIL'),
    'token' => env('PAGSEGURO_TOKEN'),
    'sandbox' => env('PAGSEGURO_SANDBOX', true),
    
    'urls' => [
        'production' => [
            'transactions' => 'https://ws.pagseguro.uol.com.br/v2/transactions',
            'sessions' => 'https://ws.pagseguro.uol.com.br/v2/sessions',
            'checkout' => 'https://ws.pagseguro.uol.com.br/v2/checkout',
        ],
        'sandbox' => [
            'transactions' => 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions',
            'sessions' => 'https://ws.sandbox.pagseguro.uol.com.br/v2/sessions',
            'checkout' => 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout',
        ],
    ],

    'notification_url' => env('PAGSEGURO_NOTIFICATION_URL'),
    'redirect_url' => env('PAGSEGURO_REDIRECT_URL'),

];