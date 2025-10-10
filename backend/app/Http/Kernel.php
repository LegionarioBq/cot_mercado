<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware global executado em todas as requisições.
     */
    protected $middleware = [
        // ✅ HandleCors deve vir primeiro
        \Illuminate\Http\Middleware\HandleCors::class,

        // Detecta proxies e IPs reais
        \Illuminate\Http\Middleware\TrustProxies::class,

        // 🔒 Evita acesso durante manutenção
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Limita tamanho máximo de uploads
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Sanitiza strings e remove campos vazios
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupos de middleware por tipo de rota (web / api).
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
            // ✅ Necessário para integração frontend (SPA React + Laravel Sanctum)
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            // Limita número de requisições
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',

            // Habilita parâmetros {id} e injeção automática de models
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middleware individuais que podem ser usados nas rotas.
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
