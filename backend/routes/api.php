<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AuthController;

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
