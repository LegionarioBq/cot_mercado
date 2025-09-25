<?php

use Illuminate\Support\Facades\Route;

// Redireciona a raiz "/" para "/api"
Route::get('/', function () {
    return redirect('/api');
});
