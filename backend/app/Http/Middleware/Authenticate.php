<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
     /**
     * Manipula requisições não autenticadas.
     * Retorna JSON 401 para a API, sem redirecionar para nenhuma rota do Laravel.
     * O frontend (React/Vite) é responsável por exibir a tela de login.
     */
    protected function redirectTo($request)
    {
        // Retorna JSON se a requisição for API ou AJAX
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'error' => 'Não autenticado',
                'message' => 'Token inválido ou ausente. Faça login novamente.'
            ], 401));
        }

         // Caso o frontend web (React/Vite) esteja servindo o login na rota /
        // o Laravel não deve tentar redirecionar para uma rota interna 'login',
        // apenas deixa o frontend cuidar disso.
        return null;
    }
}
