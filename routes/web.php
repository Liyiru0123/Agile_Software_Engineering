<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ListeningTrainingController;
use App\Http\Controllers\WritingTrainingController;
use App\Http\Controllers\SelectionTranslationController;

// ===== 首页（Dashboard�?====
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    $today = now()->toDateString();
    $currentMonth = now()->format('Y-m');
    
    // 统计数据
    $stats = [
        'total_submissions' => \App\Models\Submission::where('user_id', $user->id)->count(),
        'total_time' => \App\Models\Submission::where('user_id', $user->id)->sum('time_spent'),
        'completed_plans' => \App\Models\UserPlan::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count(),
        'current_streak' => 0,
    ];
    
    // 本月计划（用于日历）
    $plans = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('plan_date', 'like', "$currentMonth%")
        ->with('article')
        ->get()
        ->keyBy(function($plan) {
            return \Carbon\Carbon::parse($plan->plan_date)->format('Y-m-d');
        });
    
    $todayPlan = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('plan_date', $today)
        ->with('article')
        ->first();
    
    $recentSubmissions = \App\Models\Submission::where('user_id', $user->id)
        ->with(['exercise.article'])
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();
    
    $pendingPlans = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('status', 'pending')
        ->where('plan_date', '>=', $today)
        ->with('article')
        ->orderBy('plan_date')
        ->take(5)
        ->get();

    // 🔥 过期计划日期列表（用于日历标记红色）
    $expiredPlans = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('plan_date', '<', $today)
        ->where('status', 'pending')
        ->get()
        ->map(function($plan) {
            return \Carbon\Carbon::parse($plan->plan_date)->format('Y-m-d');
        })
        ->toArray();

    // 🔥 过期计划详情列表（用于页面展示）
    $overduePlans = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('status', 'pending')
        ->where('plan_date', '<', $today)
        ->with('article')
        ->orderBy('plan_date', 'desc')
        ->take(5)
        ->get();
    
    // 所有文章（用于快速制定计划）
    $articles = \App\Models\Article::orderBy('title')->get();
    
    $favoritedArticles = \App\Models\Article::join('user_favorites', 'articles.id', '=', 'user_favorites.article_id')
        ->where('user_favorites.user_id', $user->id)
        ->select('articles.*', 'user_favorites.created_at as favorited_at')
        ->orderBy('user_favorites.created_at', 'desc')
        ->take(6)
        ->get();
    
    return view('home', compact(
        'stats', 'plans', 'todayPlan', 'recentSubmissions', 
        'pendingPlans', 'articles', 'favoritedArticles', 
        'today', 'currentMonth', 'expiredPlans', 'overduePlans'
    ));
})->name('home')->middleware('auth');

// ===== 删除计划 =====
Route::delete('/plans/{plan}', function (\App\Models\UserPlan $plan) {
    if ($plan->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    $plan->delete();
    
    return response()->json(['success' => true]);
})->name('plans.destroy')->middleware('auth');

// ===== 制定/更新计划 =====
Route::post('/plans', function (\Illuminate\Http\Request $request) {
    $user = auth()->user();
    
    $validated = $request->validate([
        'article_id' => 'required|exists:articles,id',
        'plan_date' => 'required|date',
    ]);
    
    $plan = \App\Models\UserPlan::updateOrCreate(
        [
            'user_id' => $user->id,
            'article_id' => $validated['article_id'],
            'plan_date' => $validated['plan_date'],
        ],
        ['status' => 'pending']
    );
    
    return response()->json(['success' => true, 'plan' => $plan]);
})->name('plans.store')->middleware('auth');

// ===== 更新计划状�?=====
Route::patch('/plans/{plan}', function (\Illuminate\Http\Request $request, \App\Models\UserPlan $plan) {
    if ($plan->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    $validated = $request->validate([
        'status' => 'required|in:pending,completed,skipped',
    ]);
    
    $plan->update([
        'status' => $validated['status'],
        'completed_at' => $validated['status'] === 'completed' ? now() : null,
    ]);
    
    return response()->json(['success' => true, 'plan' => $plan]);
})->name('plans.update')->middleware('auth');

// ===== 文章相关路由 =====

// 文章列表页（图书馆）
Route::get('/articles', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Article::query();
    
    if ($request->filled('favorites') && $request->favorites == '1') {
        $query->join('user_favorites', 'articles.id', '=', 'user_favorites.article_id')
              ->where('user_favorites.user_id', auth()->id());
    }
    
    // 搜索
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('content', 'LIKE', "%{$search}%");
        });
    }
    
    if ($request->filled('difficulty')) {
        $query->where('difficulty', $request->difficulty);
    }
    
    // 排序
    $sort = $request->get('sort', 'newest');
    switch ($sort) {
        case 'oldest':
            $query->orderBy('articles.id', 'asc');
            break;
        case 'title':
            $query->orderBy('articles.title', 'asc');
            break;
        case 'words':
            $query->orderBy('articles.word_count', 'desc');
            break;
        default:
            $query->orderBy('articles.id', 'desc');
    }
    
    $articles = $query->paginate(10);
    
    // 预加载当前用户的收藏文章 ID
    $favoritedArticleIds = \DB::table('user_favorites')
        ->where('user_id', auth()->id())
        ->pluck('article_id')
        ->toArray();

    return view('articles.index', compact('articles', 'favoritedArticleIds'));
})->name('articles.index')->middleware('auth');

Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show')->middleware('auth');
Route::get('/articles/{article}/listening', [ArticleController::class, 'listening'])->name('articles.listening')->middleware('auth');
Route::get('/articles/{article}/speaking', [ArticleController::class, 'speaking'])->name('articles.speaking')->middleware('auth');
Route::get('/articles/{article}/reading', [ArticleController::class, 'reading'])->name('articles.reading')->middleware('auth');
Route::get('/articles/{article}/writing', [ArticleController::class, 'writing'])->name('articles.writing')->middleware('auth');
Route::post('/articles/{article}/listening/evaluate', [ListeningTrainingController::class, 'evaluate'])->name('articles.listening.evaluate')->middleware('auth');
Route::post('/articles/{article}/writing/evaluate', [WritingTrainingController::class, 'evaluate'])->name('articles.writing.evaluate')->middleware('auth');
Route::post('/selection/translate', [SelectionTranslationController::class, 'translate'])->name('selection.translate')->middleware('auth');
Route::post('/selection/save', [SelectionTranslationController::class, 'save'])->name('selection.save')->middleware('auth');

// ===== 收藏功能路由 =====

// 切换收藏状�?- 🔥 使用原始 SQL，只插入 created_at
Route::post('/articles/{article}/toggle-favorite', function (\App\Models\Article $article) {
    $user = auth()->user();
    
    // 检查是否已收藏（使用原始查询）
    $isFavorited = \DB::table('user_favorites')
        ->where('user_id', $user->id)
        ->where('article_id', $article->id)
        ->exists();
    
    if ($isFavorited) {
        // 取消收藏 - 直接删除
        \DB::table('user_favorites')
            ->where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->delete();
        
        return response()->json(['favorited' => false, 'message' => 'Removed from favorites']);
    } else {
        // 添加收藏 - 🔥 只插�?created_at，不插入 updated_at
        \DB::table('user_favorites')->insert([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'created_at' => now(),
        ]);
        
        return response()->json(['favorited' => true, 'message' => 'Added to favorites']);
    }
})->name('articles.toggle-favorite')->middleware('auth');

// 收藏列表 - 🔥 使用原始查询
Route::get('/favorites', function () {
    $favorites = \DB::table('user_favorites')
        ->join('articles', 'user_favorites.article_id', '=', 'articles.id')
        ->where('user_favorites.user_id', auth()->id())
        ->select('articles.*')
        ->get();
    
    return view('favorites.index', compact('favorites'));
})->name('favorites.index')->middleware('auth');

// ===== 登录相关 =====
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ===== 注册相关 =====
Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

