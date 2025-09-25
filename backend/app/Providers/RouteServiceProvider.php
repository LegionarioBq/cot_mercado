<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Caminho padrão após login.
     */
    public const HOME = '/home';

    /**
     * Registra as rotas da aplicação.
     */
    public function boot(): void
    {
        $this->routes(function () {
            // Rotas da API (prefixadas com /api)
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Rotas web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
