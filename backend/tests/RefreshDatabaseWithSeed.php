<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait RefreshDatabaseWithSeed
{
    use RefreshDatabase;

    protected function refreshTestDatabase()
    {
        // Só roda se DB_REFRESH estiver habilitado
        if (env('DB_REFRESH', false)) {
            Artisan::call('migrate:fresh --seed');
        }
    }
}
