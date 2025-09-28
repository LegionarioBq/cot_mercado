<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Lista de exceções que não serão reportadas.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [];

    /**
     * Lista de inputs que nunca devem ser exibidos em erros de validação.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra handlers customizados para exceções.
     */
    public function register(): void
    {
        //
    }

    /**
     * Personaliza resposta para usuários não autenticados (401)
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'message' => 'Você precisa estar autenticado para acessar este recurso.'
        ], 401);
    }

    /**
     * Personaliza resposta para erros de autorização (403)
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => 'Você não tem permissão para executar esta ação.'
            ], 403);
        }

        return parent::render($request, $exception);
    }
}
