<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Este arquivo define rotas do tipo "web", porém neste projeto (COT_MERCADO)
| ele é usado apenas para fins de diagnóstico ou endpoints simples,
| já que o backend funciona como uma API REST. 
| Não há redirecionamento para login, apenas respostas JSON.
|
*/

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API Laravel rodando 🚀'
    ]);
});

/*
|--------------------------------------------------------------------------
| Fallback de Autenticação (para rotas protegidas sem login)
|--------------------------------------------------------------------------
|
| Caso alguma rota use o middleware 'auth' ou 'auth:sanctum' e o usuário
| não esteja autenticado, o Laravel tentará chamar route('login').
| Como esta aplicação não tem uma interface de login, interceptamos isso
| e retornamos uma resposta JSON adequada (HTTP 401).
|
*/

Route::get('/login', function (Request $request) {
    return response()->json([
        'error' => 'Não autenticado',
        'message' => 'Faça login via API token ou Sanctum antes de acessar esta rota.',
    ], 401);
})->name('login');

/*
|--------------------------------------------------------------------------
| Rota fallback (404)
|--------------------------------------------------------------------------
|
| Captura todas as rotas não definidas e retorna um JSON no padrão REST.
|
*/

Route::fallback(function () {
    return response()->json([
        'error' => 'Rota não encontrada',
        'message' => 'A rota que você tentou acessar não existe nesta API.',
    ], 404);
});
