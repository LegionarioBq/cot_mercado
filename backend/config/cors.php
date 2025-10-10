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
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Quais métodos HTTP são permitidos nas requisições vindas de outras origens.
    | '*' permite todos (GET, POST, PUT, DELETE, OPTIONS).
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Defina as origens permitidas (domínios) — inclua o endereço do seu frontend.
    | No modo local, o React/Vite roda em http://localhost:5173
    |
    */
    'allowed_origins' => ['http://localhost:5173'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Pode ser usado para expressões regulares, útil em ambientes de teste.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Quais cabeçalhos podem ser enviados do frontend para a API.
    | '*' permite todos, incluindo Authorization, Content-Type etc.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Cabeçalhos que podem ser expostos ao frontend. Normalmente não é necessário
    | adicionar nada aqui para APIs básicas.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Define quanto tempo o navegador pode cachear as permissões CORS (em segundos).
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Se verdadeiro, permite envio de cookies, tokens e headers de autenticação
    | via requisições CORS (necessário para Sanctum).
    |
    */
    'supports_credentials' => true,

];
