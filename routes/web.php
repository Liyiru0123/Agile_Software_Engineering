<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Models\Article;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. 首页：从数据库动态抓取真实内容
Route::get('/', function () {
    // 抓取最新的一篇真实文章作为推荐
    $featuredArticle = Article::latest('created_at')->first();

    // 动态提取数据库中已有的所有学科分类（去重）
    $categories = Article::select('subject')->distinct()->pluck('subject');

    // 汇总真实统计数据
    $stats = [
        'count' => Article::count(),
        'words' => Article::sum('word_count'),
        'author_count' => Article::distinct('author')->count('author')
    ];

    return view('home', compact('featuredArticle', 'categories', 'stats'));
})->name('home');

// 2. 文章列表页 (Library)
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

// 3. 文章详情页 (Reading Mode)
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');

// 4. 口语练习页面 (Speaking Mode)
Route::get('/articles/{id}/speaking', function ($id) {
    // 预加载 segments 关联数据
    $article = Article::with('segments')->findOrFail($id);
    return view('articles.speaking', compact('article'));
})->name('articles.speaking');