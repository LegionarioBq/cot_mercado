<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware global executado em todas as requisições HTTP
     * Aplica CORS, segurança, validações e limpeza de requisições.
     */
    protected $middleware = [
        // Middleware correto para aplicar o config/cors.php
        \Fruitcake\Cors\HandleCors::class,

        // Detecta proxies e define IPs reais
        \Illuminate\Http\Middleware\TrustProxies::class,

        // Evita acesso durante manutenção (modo "down")
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Limita o tamanho máximo de uploads (baseado em php.ini)
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Sanitiza strings (remove espaços extras e converte vazios em null)
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupos de middleware por tipo de rota (web / api)
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            /**
             * Middleware essencial para integração Laravel Sanctum + frontend (Vite/React/TS)
             * Permite autenticação stateful via cookies ou token Bearer.
             */
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            // Limita número de requisições (rate limiting)
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',

            // Habilita injeção automática de models e parâmetros {id}
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middleware individuais que podem ser aplicados a rotas específicas
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
