<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// ===== 首页（Dashboard）=====
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
    
    // 本月计划
    $plans = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('plan_date', 'like', "$currentMonth%")
        ->with('article')
        ->get()
        ->keyBy('plan_date');
    
    // 今天的计划
    $todayPlan = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('plan_date', $today)
        ->with('article')
        ->first();
    
    // 最近提交
    $recentSubmissions = \App\Models\Submission::where('user_id', $user->id)
        ->with(['exercise.article'])
        ->orderBy('id', 'desc')  // 🔥 改用 id 排序，避免 created_at 问题
        ->take(5)
        ->get();
    
    // 待完成计划
    $pendingPlans = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('status', 'pending')
        ->where('plan_date', '>=', $today)
        ->with('article')
        ->orderBy('plan_date')
        ->take(5)
        ->get();
    
    // 所有文章
    $articles = \App\Models\Article::orderBy('title')->get();
    
    return view('home', compact(
        'stats', 'plans', 'todayPlan', 'recentSubmissions', 'pendingPlans', 'articles'
    ));
})->name('home')->middleware('auth');

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

// ===== 更新计划状态 =====
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
    
    // 搜索
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('content', 'LIKE', "%{$search}%");
        });
    }
    
    // 难度筛选
    if ($request->filled('difficulty')) {
        $query->where('difficulty', $request->difficulty);
    }
    
    // 排序
    $sort = $request->get('sort', 'newest');
    switch ($sort) {
        case 'oldest':
            $query->orderBy('id', 'asc');
            break;
        case 'title':
            $query->orderBy('title', 'asc');
            break;
        case 'words':
            $query->orderBy('word_count', 'desc');
            break;
        default:
            $query->orderBy('id', 'desc');
    }
    
    $articles = $query->paginate(10);
    
    // 🔥 预加载收藏状态：为每篇文章添加 is_favorited 属性
    $userId = auth()->id();
// 预加载当前用户的收藏文章 ID
    $favoritedArticleIds = \DB::table('user_favorites')
        ->where('user_id', auth()->id())
        ->pluck('article_id')
        ->toArray();

    return view('articles.index', compact('articles', 'favoritedArticleIds'));
})->name('articles.index')->middleware('auth');

// 文章详情页
Route::get('/articles/{article}', function (\App\Models\Article $article) {
    return view('articles.show', compact('article'));
})->name('articles.show')->middleware('auth');

// ===== 收藏功能路由 =====

// 切换收藏状态 - 🔥 使用原始 SQL，只插入 created_at
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
        // 添加收藏 - 🔥 只插入 created_at，不插入 updated_at
        \DB::table('user_favorites')->insert([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'created_at' => now(),
            // 🔥 不插入 updated_at，因为表里没有这个字段
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