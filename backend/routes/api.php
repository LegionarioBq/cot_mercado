<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
| Essas rotas não exigem autenticação. 
| Exemplo: login, cadastro, reset de senha, etc.
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Fallback de Autenticação (para middleware auth:sanctum)
|--------------------------------------------------------------------------
| Quando o usuário tenta acessar uma rota protegida sem token válido,
| o Sanctum chama internamente o middleware Authenticate, que procura
| pela rota nomeada 'login'. 
| Aqui definimos a rota /login (GET) apenas para evitar esse erro,
| e retornamos uma resposta JSON apropriada.
*/
Route::get('/login', function (Request $request) {
    return response()->json([
        'error' => 'Não autenticado',
        'message' => 'Token de acesso inválido ou ausente. Faça login novamente para continuar.'
    ], 401);
})->name('login');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Autenticação Sanctum
|--------------------------------------------------------------------------
| Aqui usamos o middleware 'auth:sanctum' — o Sanctum valida o token JWT
| e injeta o usuário autenticado no $request->user().
*/
Route::middleware('auth:sanctum')->group(function () {

    // Logout (revoga tokens do usuário logado)
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Produtos
    |--------------------------------------------------------------------------
    | GET → leitura pública (qualquer usuário autenticado)
    | POST/PUT/DELETE → operações restritas (admin/editor)
    */
    Route::get('/produtos', [ProdutoController::class, 'index']);
    Route::get('/produtos/search', [ProdutoController::class, 'search']);
    Route::get('/produtos/{id}', [ProdutoController::class, 'show']);

    Route::post('/produtos', [ProdutoController::class, 'store']);
    Route::put('/produtos/{id}', [ProdutoController::class, 'update']);
    Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Rota Fallback (404)
|--------------------------------------------------------------------------
| Caso o cliente tente acessar uma rota inexistente da API, retornamos
| um JSON padronizado ao invés de uma página HTML.
*/
Route::fallback(function () {
    return response()->json([
        'error' => 'Rota não encontrada',
        'message' => 'A rota que você tentou acessar não existe nesta API.'
    ], 404);
});
