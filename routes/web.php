<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ListeningTrainingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\CompanionController;
use App\Http\Controllers\ReadingQuestionAttemptController;
use App\Http\Controllers\SelectionTranslationController;
use App\Http\Controllers\WritingTrainingController;
use App\Models\Article;
use App\Models\Conversation;
use App\Models\FriendRequest;
use App\Models\ForumPost;
use App\Models\ReadingHistory;
use App\Models\SelectionFavorite;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    $today = \Carbon\Carbon::today();

    try {
        $selectedDate = $request->filled('date')
            ? \Carbon\Carbon::parse((string) $request->query('date'))->startOfDay()
            : $today->copy();
    } catch (\Throwable $exception) {
        $selectedDate = $today->copy();
    }

    try {
        $currentMonth = $request->filled('month')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $request->query('month').'-01')->startOfMonth()
            : $selectedDate->copy()->startOfMonth();
    } catch (\Throwable $exception) {
        $currentMonth = $selectedDate->copy()->startOfMonth();
    }

    $monthStart = $currentMonth->copy()->startOfMonth();
    $monthEnd = $currentMonth->copy()->endOfMonth();

    $sortByDashboardPriority = "CASE status
        WHEN 'pending' THEN 0
        WHEN 'skipped' THEN 1
        WHEN 'completed' THEN 2
        ELSE 3
    END";

    $allPlans = UserPlan::query()
        ->where('user_id', $user->id)
        ->with('article')
        ->orderBy('plan_date')
        ->orderByRaw($sortByDashboardPriority)
        ->get();

    $monthPlans = $allPlans
        ->filter(fn (UserPlan $plan) => $plan->plan_date && $plan->plan_date->between($monthStart, $monthEnd));

    $calendarPlans = $monthPlans
        ->groupBy(fn (UserPlan $plan) => $plan->plan_date->toDateString())
        ->map(function ($plans, string $date) use ($today) {
            $total = $plans->count();
            $completed = $plans->where('status', 'completed')->count();
            $pending = $plans->where('status', 'pending')->count();
            $skipped = $plans->where('status', 'skipped')->count();

            return [
                'total' => $total,
                'completed' => $completed,
                'pending' => $pending,
                'skipped' => $skipped,
                'completion_rate' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
                'has_overdue' => $pending > 0 && $date < $today->toDateString(),
            ];
        });

    $selectedTasks = $allPlans
        ->filter(fn (UserPlan $plan) => $plan->plan_date && $plan->plan_date->isSameDay($selectedDate))
        ->values();

    $todayTasks = $allPlans
        ->filter(fn (UserPlan $plan) => $plan->plan_date && $plan->plan_date->isSameDay($today))
        ->values();

    $overdueTasks = $allPlans
        ->filter(fn (UserPlan $plan) => $plan->status === 'pending' && $plan->plan_date && $plan->plan_date->lt($today))
        ->take(5)
        ->values();

    $weeklyStart = $today->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
    $weeklyEnd = $today->copy()->endOfWeek(\Carbon\Carbon::MONDAY);

    $weeklyPlans = $allPlans
        ->filter(fn (UserPlan $plan) => $plan->plan_date && $plan->plan_date->between($weeklyStart, $weeklyEnd))
        ->values();

    $weeklySummary = [
        'label' => $weeklyStart->format('M j').' - '.$weeklyEnd->format('M j'),
        'total' => $weeklyPlans->count(),
        'completed' => $weeklyPlans->where('status', 'completed')->count(),
        'pending' => $weeklyPlans->where('status', 'pending')->count(),
    ];
    $weeklySummary['completion_rate'] = $weeklySummary['total'] > 0
        ? (int) round(($weeklySummary['completed'] / $weeklySummary['total']) * 100)
        : 0;

    $monthlySummary = [
        'label' => $currentMonth->format('F Y'),
        'total' => $monthPlans->count(),
        'completed' => $monthPlans->where('status', 'completed')->count(),
        'pending' => $monthPlans->where('status', 'pending')->count(),
    ];
    $monthlySummary['completion_rate'] = $monthlySummary['total'] > 0
        ? (int) round(($monthlySummary['completed'] / $monthlySummary['total']) * 100)
        : 0;

    $todaySummary = [
        'total' => $todayTasks->count(),
        'completed' => $todayTasks->where('status', 'completed')->count(),
        'pending' => $todayTasks->where('status', 'pending')->count(),
        'skipped' => $todayTasks->where('status', 'skipped')->count(),
    ];

    $favoritePlanArticles = Article::query()
        ->join('user_favorites', 'articles.id', '=', 'user_favorites.article_id')
        ->where('user_favorites.user_id', $user->id)
        ->select('articles.*', 'user_favorites.created_at as favorited_at')
        ->orderByDesc('user_favorites.created_at')
        ->get();

    $favoriteArticleIds = $favoritePlanArticles
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->all();

    $otherArticles = Article::query()
        ->when(
            count($favoriteArticleIds) > 0,
            fn ($query) => $query->whereNotIn('id', $favoriteArticleIds)
        )
        ->orderBy('title')
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
        'recent' => $history->take(3)->values(),
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
        'recent' => $notebook->take(3)->values(),
    ];

    $favoritesSummary = [
        'total' => $favoritePlanArticles->count(),
        'recent' => $favoritePlanArticles->take(3)->values(),
    ];

    $communitySummary = [
        'my_posts_count' => ForumPost::query()
            ->where('user_id', $user->id)
            ->count(),
        'saved_posts_count' => $user->favoritedForumPosts()->count(),
    ];
    return view('home', compact(
        'selectedDate',
        'currentMonth',
        'calendarPlans',
        'selectedTasks',
        'overdueTasks',
        'weeklySummary',
        'monthlySummary',
        'todaySummary',
        'favoritePlanArticles',
        'otherArticles',
        'history',
        'historySummary',
        'notebook',
        'notebookSummary',
        'favoritesSummary',
        'communitySummary',
        'today',
        'monthStart',
        'monthEnd'
    ));
})->name('home')->middleware('auth');

Route::get('/dashboard', fn () => redirect()->route('home'))->name('dashboard')->middleware('auth');
Route::get('/analysis/study', [AnalysisController::class, 'studyAnalysis'])->name('study.analysis')->middleware('auth');
Route::get('/game', function () {
    $words = DB::table('wordle_words')
        ->where('is_active', true)
        ->pluck('word')
        ->map(fn ($word) => strtoupper((string) $word))
        ->values()
        ->all();

    return view('games.wordle', compact('words'));
})->name('game.index')->middleware('auth');

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
        'plan_date' => 'required|date',
        'plan_kind' => 'required|in:article,skill,custom',
        'article_id' => 'nullable|exists:articles,id',
        'skill_type' => 'nullable|in:listening,speaking',
        'target_count' => 'nullable|integer|min:1|max:10',
        'custom_title' => 'nullable|string|max:120',
    ]);

    if ($validated['plan_kind'] === 'article' && empty($validated['article_id'])) {
        throw ValidationException::withMessages([
            'article_id' => 'Please choose an article for an article plan.',
        ]);
    }

    if ($validated['plan_kind'] === 'skill' && (empty($validated['skill_type']) || empty($validated['target_count']))) {
        throw ValidationException::withMessages([
            'skill_type' => 'Please choose a skill and target count.',
        ]);
    }

    if ($validated['plan_kind'] === 'custom' && trim((string) ($validated['custom_title'] ?? '')) === '') {
        throw ValidationException::withMessages([
            'custom_title' => 'Please enter a custom task.',
        ]);
    }

    $plan = match ($validated['plan_kind']) {
        'article' => (function () use ($user, $validated) {
            return UserPlan::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'plan_kind' => 'article',
                    'article_id' => $validated['article_id'],
                    'plan_date' => $validated['plan_date'],
                ],
                [
                    'title' => null,
                    'skill_type' => null,
                    'target_count' => null,
                    'status' => 'pending',
                ]
            );
        })(),
        'skill' => (function () use ($user, $validated) {
            return UserPlan::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'plan_kind' => 'skill',
                    'plan_date' => $validated['plan_date'],
                    'skill_type' => $validated['skill_type'],
                    'target_count' => $validated['target_count'],
                ],
                [
                    'article_id' => null,
                    'title' => ucfirst($validated['skill_type']).' practice x'.$validated['target_count'],
                    'status' => 'pending',
                ]
            );
        })(),
        default => (function () use ($user, $validated) {
            return UserPlan::query()->create([
                'user_id' => $user->id,
                'article_id' => null,
                'plan_date' => $validated['plan_date'],
                'plan_kind' => 'custom',
                'title' => trim((string) $validated['custom_title']),
                'skill_type' => null,
                'target_count' => null,
                'status' => 'pending',
            ]);
        })(),
    };

    if ($request->expectsJson()) {
        return response()->json(['success' => true, 'plan' => $plan]);
    }

    return redirect()
        ->route('home', [
            'date' => $validated['plan_date'],
            'month' => \Carbon\Carbon::parse($validated['plan_date'])->format('Y-m'),
        ])
        ->with('status', 'Study plan added.');
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

    if ($request->expectsJson()) {
        return response()->json(['success' => true, 'plan' => $plan]);
    }

    return back()->with('status', 'Plan updated.');
})->name('plans.update')->middleware('auth');

Route::get('/articles', function (Request $request) {
    $query = Article::query();

    $skill = $request->get('skill');
    $validSkills = ['listening', 'speaking', 'reading', 'writing'];

    if ($skill && in_array($skill, $validSkills, true)) {
        $query->whereHas('exercises', function ($q) use ($skill) {
            $q->where('type', $skill);
        });
    }

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

    return view('articles.index', [
        'articles' => $articles,
        'favoritedArticleIds' => $favoritedArticleIds,
        'skill' => $skill,
    ]);
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
Route::get('/games/wordle', function () {
    return redirect()->route('game.index');
})->name('games.wordle')->middleware('auth');

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
Route::get('/forum/my', [ForumController::class, 'myForum'])->name('forum.my')->middleware('auth');
Route::get('/forum/saved', [ForumController::class, 'saved'])->name('forum.saved')->middleware('auth');
Route::get('/forum/tags/create', [ForumController::class, 'createTag'])->name('forum.tags.create')->middleware('auth');
Route::post('/forum/tags', [ForumController::class, 'storeTag'])->name('forum.tags.store')->middleware('auth');
Route::delete('/forum/tags/{tag}', [ForumController::class, 'destroyTag'])->name('forum.tags.destroy')->middleware('auth');
Route::get('/forum/posts/create', [ForumController::class, 'createPost'])->name('forum.posts.create')->middleware('auth');
Route::post('/forum/posts', [ForumController::class, 'storePost'])->name('forum.posts.store')->middleware('auth');
Route::get('/forum/posts/{post}', [ForumController::class, 'show'])->name('forum.posts.show')->middleware('auth');
Route::patch('/forum/posts/{post}', [ForumController::class, 'updatePost'])->name('forum.posts.update')->middleware('auth');
Route::delete('/forum/posts/{post}', [ForumController::class, 'destroyPost'])->name('forum.posts.destroy')->middleware('auth');
Route::post('/forum/posts/{post}/pin', [ForumController::class, 'togglePostPin'])->name('forum.posts.pin')->middleware('auth');
Route::post('/forum/posts/{post}/like', [ForumController::class, 'toggleLike'])->name('forum.posts.like')->middleware('auth');
Route::post('/forum/posts/{post}/favorite', [ForumController::class, 'toggleFavorite'])->name('forum.posts.favorite')->middleware('auth');
Route::post('/forum/posts/{post}/comments', [ForumController::class, 'storeComment'])->name('forum.comments.store')->middleware('auth');
Route::post('/forum/comments/{comment}/pin', [ForumController::class, 'toggleCommentPin'])->name('forum.comments.pin')->middleware('auth');
Route::patch('/forum/comments/{comment}', [ForumController::class, 'updateComment'])->name('forum.comments.update')->middleware('auth');
Route::delete('/forum/comments/{comment}', [ForumController::class, 'destroyComment'])->name('forum.comments.destroy')->middleware('auth');

Route::get('/friends', [FriendController::class, 'index'])->name('friends.index')->middleware('auth');
Route::post('/friends/requests', [FriendController::class, 'store'])->name('friends.requests.store')->middleware('auth');
Route::post('/friends/requests/{friendRequest}/accept', [FriendController::class, 'accept'])->name('friends.requests.accept')->middleware('auth');
Route::post('/friends/requests/{friendRequest}/reject', [FriendController::class, 'reject'])->name('friends.requests.reject')->middleware('auth');
Route::delete('/friends/requests/{friendRequest}', [FriendController::class, 'cancel'])->name('friends.requests.cancel')->middleware('auth');
Route::delete('/friends/{user}', [FriendController::class, 'destroy'])->name('friends.destroy')->middleware('auth');

Route::get('/messages', [MessageController::class, 'index'])->name('messages.index')->middleware('auth');
Route::get('/messages/{conversation}', [MessageController::class, 'index'])->name('messages.show')->middleware('auth');
Route::post('/messages/start', [MessageController::class, 'start'])->name('messages.start')->middleware('auth');
Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store')->middleware('auth');

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

