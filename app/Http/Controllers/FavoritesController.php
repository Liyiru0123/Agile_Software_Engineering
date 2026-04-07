<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\UserPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller
{
    public function index(): View
    {
        $favorites = Article::query()
            ->join('user_favorites', 'articles.id', '=', 'user_favorites.article_id')
            ->where('user_favorites.user_id', auth()->id())
            ->select('articles.*', 'user_favorites.created_at as favorited_at')
            ->orderByDesc('user_favorites.created_at')
            ->paginate(12);

        return view('favorites.index', [
            'favorites' => $favorites,
        ]);
    }

    public function plan(): View
    {
        $favorites = Article::query()
            ->join('user_favorites', 'articles.id', '=', 'user_favorites.article_id')
            ->where('user_favorites.user_id', auth()->id())
            ->select('articles.*', 'user_favorites.created_at as favorited_at')
            ->orderByDesc('user_favorites.created_at')
            ->get();

        $plannedArticleIds = UserPlan::query()
            ->where('user_id', auth()->id())
            ->pluck('article_id')
            ->all();

        return view('favorites.plan', [
            'favorites' => $favorites,
            'plannedArticleIds' => $plannedArticleIds,
        ]);
    }

    public function storePlan(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'article_ids' => ['required', 'array', 'min:1'],
            'article_ids.*' => ['integer', 'exists:articles,id'],
            'plan_date' => ['required', 'date'],
        ]);

        $favoriteIds = DB::table('user_favorites')
            ->where('user_id', auth()->id())
            ->pluck('article_id')
            ->all();

        $selectedIds = collect($payload['article_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => in_array($id, $favoriteIds, true))
            ->unique()
            ->values();

        foreach ($selectedIds as $articleId) {
            UserPlan::query()->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'plan_kind' => 'article',
                    'article_id' => $articleId,
                    'plan_date' => $payload['plan_date'],
                ],
                [
                    'title' => null,
                    'skill_type' => null,
                    'target_count' => null,
                    'status' => 'pending',
                ]
            );
        }

        return redirect()
            ->route('favorites.plan')
            ->with('status', 'Study plans created from favorites.');
    }
}
