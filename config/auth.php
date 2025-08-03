<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION DEFAULTS
    |--------------------------------------------------------------------------
    | CONFIGURAÇÃO PADRÃO PARA GUARD E PASSWORD BROKER
    */
    'defaults' => [
        'guard' => 'web',                  // GUARD PADRÃO (SESSION)
        'passwords' => 'usuarios',         // BROKER PADRÃO PARA REDEFINIÇÃO DE SENHA
    ],

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION GUARDS
    |--------------------------------------------------------------------------
    | DEFINIÇÃO DOS GUARDS DE AUTENTICAÇÃO DISPONÍVEIS
    */
    'guards' => [
        'web' => [
            'driver' => 'session',         // DRIVER DE SESSÃO PARA AUTENTICAÇÃO WEB
            'provider' => 'usuarios',      // PROVIDER QUE SERÁ UTILIZADO
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | USER PROVIDERS
    |--------------------------------------------------------------------------
    | CONFIGURAÇÃO DE COMO OS USUÁRIOS SÃO RECUPERADOS DO BANCO
    */
    'providers' => [
        'usuarios' => [
            'driver' => 'eloquent',        // UTILIZA ELOQUENT ORM
            'model' => App\Models\Usuario::class, // MODEL PERSONALIZADO
        ],

        // PROVIDER ALTERNATIVO (CASO NECESSÁRIO)
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'usuarios',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | RESETTING PASSWORDS
    |--------------------------------------------------------------------------
    | CONFIGURAÇÃO PARA REDEFINIÇÃO DE SENHAS
    */
    'passwords' => [
        'usuarios' => [
            'provider' => 'usuarios',      // REFERENCIA O PROVIDER ACIMA
            'table' => 'password_reset_tokens', // TABELA PARA TOKENS
            'expire' => 60,                // TEMPO DE EXPIRAÇÃO EM MINUTOS
            'throttle' => 60,              // TEMPO DE ESPERA ENTRE SOLICITAÇÕES
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | PASSWORD CONFIRMATION TIMEOUT
    |--------------------------------------------------------------------------
    | TEMPO PARA EXPIRAR A CONFIRMAÇÃO DE SENHA
    */
    'password_timeout' => 10800,           // 3 HORAS EM SEGUNDOS
];