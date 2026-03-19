<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReadingHistoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WrongQuestionController;
use App\Http\Controllers\ReadingAnswerController; // Import reading answer controller

// Home page
Route::get('/', [ArticleController::class, 'home'])->name('home');

// Article routes
Route::resource('articles', ArticleController::class)->only(['index', 'show']);

// Favorite routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('favorites/batch-delete', [FavoriteController::class, 'batchDelete'])->name('favorites.batch-delete');
    Route::post('favorites/{article_id}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

// Reading history routes (auth required)
Route::middleware('auth')->group(function () {
    Route::post('reading-history/{article_id}/start', [ReadingHistoryController::class, 'start']);
    Route::post('reading-history/{article_id}/progress', [ReadingHistoryController::class, 'updateProgress']);
    Route::get('reading-history', [ReadingHistoryController::class, 'index'])->name('history.index');
    Route::delete('reading-history/{article_id}', [ReadingHistoryController::class, 'destroy'])->name('history.destroy');
    Route::post('reading-history/clear', [ReadingHistoryController::class, 'clear'])->name('history.clear');
});

// Reading answer routes (auth required)
Route::middleware('auth')->group(function () {
    Route::post('reading-answers/submit', [ReadingAnswerController::class, 'submit'])
        ->name('reading-answers.submit');
    Route::post('reading-answers/add-to-wrong', [ReadingAnswerController::class, 'addToWrong'])
        ->name('reading-answers.add-to-wrong');
});

// Wrong questions routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('wrong-questions', [WrongQuestionController::class, 'index'])->name('wrong-questions.index');
    Route::post('wrong-questions/{question_id}', [WrongQuestionController::class, 'toggle'])->name('wrong-questions.toggle');
    Route::delete('wrong-questions/{wrongQuestion}', [WrongQuestionController::class, 'destroy'])->name('wrong-questions.destroy');
    Route::post('wrong-questions/clear', [WrongQuestionController::class, 'clear'])->name('wrong-questions.clear');
});

// Authentication routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// Logout route (auth required)
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin routes (auth + admin required)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.articles.index');
    })->name('dashboard');

    Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
    Route::post('articles/batch-delete', [\App\Http\Controllers\Admin\ArticleController::class, 'batchDelete'])->name('articles.batch-delete');
    Route::post('articles/{article}/toggle-status', [\App\Http\Controllers\Admin\ArticleController::class, 'toggleStatus'])->name('articles.toggle-status');

    Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class);
    Route::get('questions/article/{article_id}', [\App\Http\Controllers\Admin\QuestionController::class, 'byArticle'])->name('questions.by-article');
    Route::post('questions/batch-delete', [\App\Http\Controllers\Admin\QuestionController::class, 'batchDelete'])->name('questions.batch-delete');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'edit', 'update', 'destroy']);
});