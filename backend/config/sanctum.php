<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Esses domínios poderão manter autenticação stateful (cookies/session).
    | Incluímos o domínio do frontend (Vite) e o backend local.
    |
    */
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', implode(',', [
        'localhost',
        'localhost:5173',     // ✅ Frontend Vite
        '127.0.0.1',
        '127.0.0.1:8000',     // ✅ Backend API local
        '::1',
    ]))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Lista de guards usados para autenticação via Sanctum.
    | "web" é o padrão e necessário para login/token.
    |
    */
    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiração dos Tokens
    |--------------------------------------------------------------------------
    |
    | Define o tempo de expiração em minutos (null = nunca expira).
    | Você pode ajustar no futuro para expirar tokens automaticamente.
    |
    */
    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Prefixo dos Tokens
    |--------------------------------------------------------------------------
    |
    | (Opcional) Pode ser usado para diferenciar tokens Sanctum em logs/scans.
    |
    */
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Middleware do Sanctum
    |--------------------------------------------------------------------------
    |
    | Middlewares usados para autenticação de SPAs e rotas stateful.
    |
    */
    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
