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
    
    // IMPORTANTE: Altere para false para usar APIs reais
    'sandbox_mode' => env('OPEN_FINANCE_SANDBOX', true),
    
    // Configuração para produção
    'production' => [
        'use_real_apis' => env('OPEN_FINANCE_USE_REAL_APIS', false),
        'certificates_path' => env('OPEN_FINANCE_CERTIFICATES_PATH', storage_path('certificates')),
        'mtls_cert' => env('OPEN_FINANCE_MTLS_CERT'),
        'mtls_key' => env('OPEN_FINANCE_MTLS_KEY'),
    ],
    
    // URLs específicas por banco (produção)
    'bank_endpoints' => [
        '001' => [ // Banco do Brasil
            'authorization_server' => 'https://oauth.bb.com.br',
            'resource_server' => 'https://api.bb.com.br/open-banking',
        ],
        '033' => [ // Santander  
            'authorization_server' => 'https://trust-openbanking.santander.com.br',
            'resource_server' => 'https://trust-openbanking.santander.com.br/api/opin/v1',
        ],
        '104' => [ // Caixa
            'authorization_server' => 'https://openbanking.caixa.gov.br',
            'resource_server' => 'https://api.caixa.gov.br/openbanking',
        ],
        '237' => [ // Bradesco
            'authorization_server' => 'https://proxy.api.prebanco.com.br/auth',
            'resource_server' => 'https://proxy.api.prebanco.com.br/openbanking',
        ],
        '341' => [ // Itaú
            'authorization_server' => 'https://sts.itau.com.br',
            'resource_server' => 'https://secure.api.itau/open-banking',
        ],
        '260' => [ // Nubank
            'authorization_server' => 'https://prod-s0-webapp-proxy.nubank.com.br',
            'resource_server' => 'https://prod-s0-webapp-proxy.nubank.com.br/api/open-banking',
        ],
    ],
    
    'supported_banks' => [
        '001' => 'Banco do Brasil',
        '033' => 'Santander',
        '104' => 'Caixa Econômica Federal',
        '237' => 'Bradesco',
        '341' => 'Itaú Unibanco',
        '422' => 'Banco Safra',
        '756' => 'Sicoob',
        '077' => 'Banco Inter',
        '260' => 'Nu Pagamentos (Nubank)',
        '336' => 'Banco C6'
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
    ]
];