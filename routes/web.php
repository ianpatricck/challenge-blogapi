<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Bem vindo(a) ao Blog API',
        'description' => 'Essa é uma API Rest construída a partir de um projeto Laravel que simula um blog minificado.',
        'docs' => 'http://localhost:8000/api/documentation'
    ]);
});
