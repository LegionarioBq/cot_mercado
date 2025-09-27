<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;

// Rota de teste
Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API Laravel está respondendo 🚀'
    ]);
});

// 👉 Rota de busca precisa vir ANTES
Route::get('/produtos/search', [ProdutoController::class, 'search']);

// Rotas de produtos (CRUD)
Route::apiResource('produtos', ProdutoController::class);
