<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\FavoriteApiController;
use App\Http\Controllers\Api\ListeningProgressApiController;
use App\Http\Controllers\Api\QuestionApiController;
use App\Http\Controllers\Api\ReadingHistoryApiController;
use App\Http\Controllers\Api\TagApiController;
use App\Http\Controllers\Api\VocabularyNoteApiController;
use App\Http\Controllers\WordBookController;
use App\Http\Controllers\LearningRecordController;

use Illuminate\Support\Facades\Route;

Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleApiController::class, 'index']);
    Route::post('/', [ArticleApiController::class, 'store']);
    Route::get('/{article}/reading', [ArticleApiController::class, 'reading']);
    Route::get('/{article}/audio', [ArticleApiController::class, 'audio']);
    Route::get('/{article}/questions', [QuestionApiController::class, 'index']);
    Route::get('/{article}/listening-progress', [ListeningProgressApiController::class, 'show']);
    Route::post('/{article}/listening-progress', [ListeningProgressApiController::class, 'store']);
    Route::get('/{article}', [ArticleApiController::class, 'show']);
    Route::put('/{article}', [ArticleApiController::class, 'update']);
    Route::delete('/{article}', [ArticleApiController::class, 'destroy']);
});

Route::prefix('tags')->group(function () {
    Route::get('/', [TagApiController::class, 'index']);
    Route::post('/', [TagApiController::class, 'store']);
});

Route::prefix('favorites')->group(function () {
    Route::get('/', [FavoriteApiController::class, 'index']);
    Route::post('/', [FavoriteApiController::class, 'store']);
    Route::delete('/{article}', [FavoriteApiController::class, 'destroy']);
});

Route::prefix('reading-history')->group(function () {
    Route::get('/', [ReadingHistoryApiController::class, 'index']);
    Route::post('/', [ReadingHistoryApiController::class, 'store']);
});

Route::prefix('vocabulary-notes')->group(function () {
    Route::get('/', [VocabularyNoteApiController::class, 'index']);
    Route::post('/', [VocabularyNoteApiController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/word-book', [WordBookController::class, 'store']);

    Route::get('/word-book', [WordBookController::class, 'index']);

    Route::put('/word-book/{id}', [WordBookController::class, 'update']);

    Route::delete('/word-book/{id}', [WordBookController::class, 'destroy']);
  
    Route::post('/word-book/batch', [WordBookController::class, 'batchStore']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/learning/start', [LearningRecordController::class, 'startLearning']); 
    Route::post('/learning/end', [LearningRecordController::class, 'endLearning']);   
    Route::get('/learning/stat', [LearningRecordController::class, 'getLearningStat']); 
    Route::get('/learning/trend', [LearningRecordController::class, 'getLearningTrend']); 
});