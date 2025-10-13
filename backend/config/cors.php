<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | Defina quais rotas devem permitir CORS. Normalmente incluímos as rotas
    | da API, login/logout e o endpoint Sanctum CSRF (caso seja usado).
    |
    */
    'paths' => [
        'api/*',
        'login',
        'produtos',
        'logout',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Métodos HTTP permitidos nas requisições vindas de outras origens.
    | '*' libera todos (GET, POST, PUT, DELETE, OPTIONS).
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Domínios permitidos a acessar esta API.
    | Puxa automaticamente do .env (CORS_ALLOWED_ORIGINS).
    | Pode incluir localhost, 127.0.0.1, ou o container (app_produtos_front).
    | 'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173')),
    |
    */
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://app_produtos_front:5173',
    ],
    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Pode ser usado para padrões de subdomínios (opcional).
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Cabeçalhos aceitos das requisições do frontend.
    | '*' libera todos, incluindo Authorization (Bearer Token).
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Cabeçalhos que podem ser expostos ao frontend.
    | Normalmente não é necessário adicionar nada aqui.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Tempo (em segundos) que o navegador pode cachear a política CORS.
    | 0 = sempre verificar.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Se true, permite envio de tokens (Bearer), cookies e cabeçalhos CORS.
    | É obrigatório para autenticação via Sanctum.
    |
    */
    'supports_credentials' => true,

];
