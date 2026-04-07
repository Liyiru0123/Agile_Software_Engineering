<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ListeningTrainingController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\CompanionController;
use App\Http\Controllers\ReadingQuestionAttemptController;
use App\Http\Controllers\SelectionTranslationController;
use App\Http\Controllers\WritingTrainingController;
use App\Models\Article;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\ReadingHistory;
use App\Models\SelectionFavorite;
use App\Models\Submission;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    $today = now()->toDateString();
    $currentMonth = now()->format('Y-m');

    $stats = [
        'total_submissions' => Submission::where('user_id', $user->id)->count(),
        'total_time' => Submission::where('user_id', $user->id)->sum('time_spent'),
        'completed_plans' => UserPlan::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count(),
        'current_streak' => 0,
    ];

    $plans = UserPlan::where('user_id', $user->id)
        ->where('plan_date', 'like', "$currentMonth%")
        ->with('article')
        ->get()
        ->keyBy(fn (UserPlan $plan) => \Carbon\Carbon::parse($plan->plan_date)->format('Y-m-d'));

    $todayPlan = UserPlan::where('user_id', $user->id)
        ->where('plan_date', $today)
        ->with('article')
        ->first();

    $recentSubmissions = Submission::where('user_id', $user->id)
        ->with(['exercise.article'])
        ->orderByDesc('id')
        ->take(5)
        ->get();

    $pendingPlans = UserPlan::where('user_id', $user->id)
        ->where('status', 'pending')
        ->where('plan_date', '>=', $today)
        ->with('article')
        ->orderBy('plan_date')
        ->take(5)
        ->get();

    $expiredPlans = UserPlan::where('user_id', $user->id)
        ->where('plan_date', '<', $today)
        ->where('status', 'pending')
        ->get()
        ->map(fn (UserPlan $plan) => \Carbon\Carbon::parse($plan->plan_date)->format('Y-m-d'))
        ->toArray();

    $overduePlans = UserPlan::where('user_id', $user->id)
        ->where('status', 'pending')
        ->where('plan_date', '<', $today)
        ->with('article')
        ->orderByDesc('plan_date')
        ->take(5)
        ->get();

    $articles = Article::orderBy('title')->get();

    $favoritedArticles = Article::join('user_favorites', 'articles.id', '=', 'user_favorites.article_id')
        ->where('user_favorites.user_id', $user->id)
        ->select('articles.*', 'user_favorites.created_at as favorited_at')
        ->orderByDesc('user_favorites.created_at')
        ->take(6)
        ->get();

    $history = ReadingHistory::query()
        ->with('article')
        ->where('user_id', $user->id)
        ->orderByDesc('last_viewed_at')
        ->take(5)
        ->get()
        ->map(fn (ReadingHistory $item) => [
            'title' => $item->article?->title ?? 'Untitled Article',
            'page_label' => $item->page_label,
            'continue_url' => $item->continue_url,
            'last_viewed_at' => optional($item->last_viewed_at)?->diffForHumans(),
        ]);

    $activeDays7d = ReadingHistory::query()
        ->where('user_id', $user->id)
        ->where('last_viewed_at', '>=', now()->subDays(6)->startOfDay())
        ->get()
        ->pluck('last_viewed_at')
        ->filter()
        ->map(fn ($timestamp) => $timestamp->toDateString())
        ->unique()
        ->count();

    $latestHistory = ReadingHistory::query()
        ->where('user_id', $user->id)
        ->orderByDesc('last_viewed_at')
        ->first();

    $historySummary = [
        'recent' => $history,
        'active_days_7d' => $activeDays7d,
        'continue_url' => $latestHistory?->continue_url ?? route('articles.index'),
    ];

    $notebook = SelectionFavorite::query()
        ->with('article')
        ->where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->take(3)
        ->get()
        ->map(fn (SelectionFavorite $item) => [
            'text' => $item->selected_text,
            'article_title' => $item->article?->title ?? 'Untitled Article',
            'article_url' => route('articles.show', $item->article_id),
        ]);

    $notebookSummary = [
        'new_this_week' => SelectionFavorite::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->count(),
        'review_pending' => SelectionFavorite::query()
            ->where('user_id', $user->id)
            ->count(),
        'recent' => $notebook,
    ];

    $forumPostsSummary = [
        'count' => ForumPost::query()
            ->where('user_id', $user->id)
            ->count(),
        'recent' => ForumPost::query()
            ->with('tag:id,name,slug')
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (ForumPost $post) => [
                'title' => $post->title,
                'tag_name' => $post->tag?->name,
                'url' => route('forum.posts.show', $post),
                'created_at' => optional($post->created_at)?->diffForHumans(),
            ]),
    ];

    $forumCommentsSummary = [
        'count' => ForumComment::query()
            ->where('user_id', $user->id)
            ->count(),
        'recent' => ForumComment::query()
            ->with('post:id,title')
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (ForumComment $comment) => [
                'excerpt' => \Illuminate\Support\Str::limit($comment->body, 90),
                'post_title' => $comment->post?->title ?? 'Forum Post',
                'url' => $comment->post
                    ? route('forum.posts.show', $comment->post).'#comment-'.$comment->id
                    : route('forum.index'),
                'created_at' => optional($comment->created_at)?->diffForHumans(),
            ]),
    ];

    $forumSavedSummary = [
        'count' => $user->favoritedForumPosts()->count(),
        'recent' => $user->favoritedForumPosts()
            ->with(['tag:id,name,slug', 'user:id,name'])
            ->latest('forum_post_favorites.created_at')
            ->take(3)
            ->get()
            ->map(fn (ForumPost $post) => [
                'title' => $post->title,
                'tag_name' => $post->tag?->name,
                'author_name' => $post->user?->name ?? 'Unknown',
                'url' => route('forum.posts.show', $post),
                'saved_at' => optional($post->pivot?->created_at)?->diffForHumans(),
            ]),
    ];

    return view('home', compact(
        'stats',
        'plans',
        'todayPlan',
        'recentSubmissions',
        'pendingPlans',
        'articles',
        'favoritedArticles',
        'history',
        'historySummary',
        'notebook',
        'notebookSummary',
        'forumPostsSummary',
        'forumCommentsSummary',
        'forumSavedSummary',
        'today',
        'currentMonth',
        'expiredPlans',
        'overduePlans'
    ));
})->name('home')->middleware('auth');

Route::get('/dashboard', fn () => redirect()->route('home'))->name('dashboard')->middleware('auth');
Route::get('/analysis/study', [AnalysisController::class, 'studyAnalysis'])->name('study.analysis')->middleware('auth');

Route::delete('/plans/{plan}', function (UserPlan $plan) {
    if ($plan->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $plan->delete();

    return response()->json(['success' => true]);
})->name('plans.destroy')->middleware('auth');

Route::post('/plans', function (Request $request) {
    $user = auth()->user();

    $validated = $request->validate([
        'article_id' => 'required|exists:articles,id',
        'plan_date' => 'required|date',
    ]);

    $plan = UserPlan::updateOrCreate(
        [
            'user_id' => $user->id,
            'article_id' => $validated['article_id'],
            'plan_date' => $validated['plan_date'],
        ],
        ['status' => 'pending']
    );

    return response()->json(['success' => true, 'plan' => $plan]);
})->name('plans.store')->middleware('auth');

Route::patch('/plans/{plan}', function (Request $request, UserPlan $plan) {
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

Route::get('/articles', function (Request $request) {
    $query = Article::query();

    if ($request->filled('favorites') && $request->favorites === '1') {
        $query->join('user_favorites', 'articles.id', '=', 'user_favorites.article_id')
            ->where('user_favorites.user_id', auth()->id());
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
                ->orWhere('content', 'LIKE', "%{$search}%");
        });
    }

    if ($request->filled('difficulty')) {
        $query->where('difficulty', $request->difficulty);
    }

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
            break;
    }

    $articles = $query->paginate(12);
    $favoritedArticleIds = DB::table('user_favorites')
        ->where('user_id', auth()->id())
        ->pluck('article_id')
        ->toArray();

    return view('articles.index', compact('articles', 'favoritedArticleIds'));
})->name('articles.index')->middleware('auth');

Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show')->middleware('auth');
Route::get('/articles/{article}/listening', [ArticleController::class, 'listening'])->name('articles.listening')->middleware('auth');
Route::get('/articles/{article}/speaking', [ArticleController::class, 'speaking'])->name('articles.speaking')->middleware('auth');
Route::post('/articles/{article}/speaking/submit', [ArticleController::class, 'submitSpeaking'])->name('articles.speaking.submit')->middleware('auth');
Route::get('/articles/{article}/reading', [ArticleController::class, 'reading'])->name('articles.reading')->middleware('auth');
Route::get('/articles/{article}/reading/questions', [ReadingQuestionAttemptController::class, 'index'])->name('articles.reading.questions')->middleware('auth');
Route::post('/articles/{article}/reading/submit', [ReadingQuestionAttemptController::class, 'submit'])->name('articles.reading.submit')->middleware('auth');
Route::get('/articles/{article}/writing', [ArticleController::class, 'writing'])->name('articles.writing')->middleware('auth');
Route::post('/articles/{article}/listening/evaluate', [ListeningTrainingController::class, 'evaluate'])->name('articles.listening.evaluate')->middleware('auth');
Route::post('/articles/{article}/writing/evaluate', [WritingTrainingController::class, 'evaluate'])->name('articles.writing.evaluate')->middleware('auth');
Route::post('/selection/translate', [SelectionTranslationController::class, 'translate'])->name('selection.translate')->middleware('auth');
Route::post('/selection/save', [SelectionTranslationController::class, 'save'])->name('selection.save')->middleware('auth');

Route::post('/articles/{article}/toggle-favorite', function (Article $article) {
    $user = auth()->user();

    $isFavorited = DB::table('user_favorites')
        ->where('user_id', $user->id)
        ->where('article_id', $article->id)
        ->exists();

    if ($isFavorited) {
        DB::table('user_favorites')
            ->where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->delete();

        return response()->json(['favorited' => false, 'message' => 'Removed from favorites']);
    }

    DB::table('user_favorites')->insert([
        'user_id' => $user->id,
        'article_id' => $article->id,
        'created_at' => now(),
    ]);

    return response()->json(['favorited' => true, 'message' => 'Added to favorites']);
})->name('articles.toggle-favorite')->middleware('auth');

Route::get('/forum', [ForumController::class, 'index'])->name('forum.index')->middleware('auth');
Route::get('/forum/saved', [ForumController::class, 'saved'])->name('forum.saved')->middleware('auth');
Route::get('/forum/tags/create', [ForumController::class, 'createTag'])->name('forum.tags.create')->middleware('auth');
Route::post('/forum/tags', [ForumController::class, 'storeTag'])->name('forum.tags.store')->middleware('auth');
Route::delete('/forum/tags/{tag}', [ForumController::class, 'destroyTag'])->name('forum.tags.destroy')->middleware('auth');
Route::get('/forum/posts/create', [ForumController::class, 'createPost'])->name('forum.posts.create')->middleware('auth');
Route::post('/forum/posts', [ForumController::class, 'storePost'])->name('forum.posts.store')->middleware('auth');
Route::get('/forum/posts/{post}', [ForumController::class, 'show'])->name('forum.posts.show')->middleware('auth');
Route::patch('/forum/posts/{post}', [ForumController::class, 'updatePost'])->name('forum.posts.update')->middleware('auth');
Route::delete('/forum/posts/{post}', [ForumController::class, 'destroyPost'])->name('forum.posts.destroy')->middleware('auth');
Route::post('/forum/posts/{post}/like', [ForumController::class, 'toggleLike'])->name('forum.posts.like')->middleware('auth');
Route::post('/forum/posts/{post}/favorite', [ForumController::class, 'toggleFavorite'])->name('forum.posts.favorite')->middleware('auth');
Route::post('/forum/posts/{post}/comments', [ForumController::class, 'storeComment'])->name('forum.comments.store')->middleware('auth');
Route::patch('/forum/comments/{comment}', [ForumController::class, 'updateComment'])->name('forum.comments.update')->middleware('auth');
Route::delete('/forum/comments/{comment}', [ForumController::class, 'destroyComment'])->name('forum.comments.destroy')->middleware('auth');

Route::get('/history', [HistoryController::class, 'index'])->name('history.index')->middleware('auth');
Route::get('/history/continue', [HistoryController::class, 'continue'])->name('history.continue')->middleware('auth');
Route::get('/notebook', [NotebookController::class, 'index'])->name('notebook.index')->middleware('auth');
Route::get('/notebook/review', [NotebookController::class, 'review'])->name('notebook.review')->middleware('auth');

Route::get('/favorites', [FavoritesController::class, 'index'])->name('favorites.index')->middleware('auth');
Route::get('/favorites/plan', [FavoritesController::class, 'plan'])->name('favorites.plan')->middleware('auth');
Route::post('/favorites/plan', [FavoritesController::class, 'storePlan'])->name('favorites.plan.store')->middleware('auth');
Route::get('/companion', [CompanionController::class, 'index'])->name('companion.index')->middleware('auth');
Route::post('/companion/shop/{item}/purchase', [CompanionController::class, 'purchase'])->name('companion.purchase')->middleware('auth');
Route::post('/companion/shop/{item}/equip', [CompanionController::class, 'equip'])->name('companion.equip')->middleware('auth');

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
