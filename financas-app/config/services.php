<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Open Finance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Open Finance integration with Brazilian banks
    |
    */

    'base_url' => env('OPEN_FINANCE_BASE_URL', 'https://api.openfinancebrasil.org.br'),
    
    'client_id' => env('OPEN_FINANCE_CLIENT_ID', 'your-client-id'),
    
    'client_secret' => env('OPEN_FINANCE_CLIENT_SECRET', 'your-client-secret'),
    
    'redirect_uri' => env('OPEN_FINANCE_REDIRECT_URI', 'http://localhost:8000/bank/integration/callback'),
    
    'sandbox_mode' => env('OPEN_FINANCE_SANDBOX', true),
    
    'supported_banks' => [
        '001' => [
            'name' => 'Banco do Brasil',
            'logo' => '/images/banks/bb.png',
            'color' => '#ffd700'
        ],
        '033' => [
            'name' => 'Santander',
            'logo' => '/images/banks/santander.png',
            'color' => '#ec0000'
        ],
        '104' => [
            'name' => 'Caixa Econômica Federal',
            'logo' => '/images/banks/caixa.png',
            'color' => '#0066cc'
        ],
        '237' => [
            'name' => 'Bradesco',
            'logo' => '/images/banks/bradesco.png',
            'color' => '#cc092f'
        ],
        '341' => [
            'name' => 'Itaú Unibanco',
            'logo' => '/images/banks/itau.png',
            'color' => '#ff6600'
        ],
        '422' => [
            'name' => 'Banco Safra',
            'logo' => '/images/banks/safra.png',
            'color' => '#1e3c8b'
        ],
        '756' => [
            'name' => 'Sicoob',
            'logo' => '/images/banks/sicoob.png',
            'color' => '#00a651'
        ],
        '077' => [
            'name' => 'Banco Inter',
            'logo' => '/images/banks/inter.png',
            'color' => '#ff7a00'
        ],
        '260' => [
            'name' => 'Nu Pagamentos (Nubank)',
            'logo' => '/images/banks/nubank.png',
            'color' => '#8a05be'
        ],
        '336' => [
            'name' => 'Banco C6',
            'logo' => '/images/banks/c6.png',
            'color' => '#ffcd00'
        ]
    ],
    
    'default_permissions' => [
        'ACCOUNTS_READ',
        'ACCOUNTS_BALANCES_READ',
        'RESOURCES_READ'
    ],
    
    'sync_settings' => [
        'default_days_back' => 30,
        'max_days_back' => 365,
        'batch_size' => 100,
        'rate_limit_per_minute' => 60,
        'timeout_seconds' => 30
    ],
    
    'webhook' => [
        'enabled' => env('OPEN_FINANCE_WEBHOOK_ENABLED', false),
        'url' => env('OPEN_FINANCE_WEBHOOK_URL', ''),
        'secret' => env('OPEN_FINANCE_WEBHOOK_SECRET', '')
    ]
];