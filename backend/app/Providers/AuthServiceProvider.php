<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 🔐 Permissão para gerenciar produtos (criar/editar)
        Gate::define('manage-produtos', function (User $user) {
            return in_array($user->type, ['admin', 'editor']);
        });

        // 🔐 Admin e editor podem excluir
        Gate::define('delete-produtos', function (User $user) {
            return in_array($user->type, ['admin', 'editor']);
        });
    }
}
