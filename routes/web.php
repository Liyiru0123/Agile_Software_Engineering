<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

Route::get('/', function () {
    return view('home');
});

Route::get('/articles', [ArticleController::class, 'index']);

Route::get('/articles/{id}', [ArticleController::class, 'show']);