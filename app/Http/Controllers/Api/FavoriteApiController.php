<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteApiController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
        ]);

        $favorites = Favorite::query()
            ->with('article')
            ->where('user_id', $payload['user_id'])
            ->get()
            ->map(fn (Favorite $favorite) => [
                'user_id' => $favorite->user_id,
                'article_id' => $favorite->article_id,
                'article_title' => $favorite->article?->title,
                'created_at' => optional($favorite->created_at)?->toISOString(),
            ]);

        return $this->success($favorites);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
            'article_id' => ['required', 'integer', 'exists:articles,article_id'],
        ]);

        $favorite = Favorite::query()->firstOrCreate($payload);

        return $this->success([
            'user_id' => $favorite->user_id,
            'article_id' => $favorite->article_id,
        ], 'favorite created', 201);
    }

    public function destroy(Request $request, int $articleId): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
        ]);

        Favorite::query()
            ->where('user_id', $payload['user_id'])
            ->where('article_id', $articleId)
            ->delete();

        return $this->success(null, 'favorite deleted');
    }
}
