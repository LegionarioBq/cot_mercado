<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;

// Rota de teste (verificar se a API está funcionando)
Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API Laravel está respondendo 🚀'
    ]);
});

// CRUD de produtos
Route::apiResource('produtos', ProdutoController::class);
