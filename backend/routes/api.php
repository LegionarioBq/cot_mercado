<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AuthController;

// 🔓 Público
Route::post('/login', [AuthController::class, 'login']);

// 🔐 Requer login
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);

    // Produtos - qualquer usuário autenticado pode visualizar
    Route::get('/produtos', [ProdutoController::class, 'index']);
    Route::get('/produtos/search', [ProdutoController::class, 'search']);
    Route::get('/produtos/{id}', [ProdutoController::class, 'show']);

    // Produtos - admin/editor
    Route::post('/produtos', [ProdutoController::class, 'store']);
    Route::put('/produtos/{id}', [ProdutoController::class, 'update']);

    // Produtos - apenas admin
    Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy']);
});
