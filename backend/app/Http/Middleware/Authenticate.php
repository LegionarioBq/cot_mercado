<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // 🔹 Se a rota pertence à API
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não está logado ou o token expirou.',
                'action'  => 'logout'
            ], 401);
        }

        // 🔹 Para acesso web, apenas redireciona para a raiz literal "/"
        return '/';
    }
}
