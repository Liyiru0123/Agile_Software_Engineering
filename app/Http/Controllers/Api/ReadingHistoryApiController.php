<?php

namespace App\Http\Controllers\Api;

use App\Models\ReadingHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReadingHistoryApiController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
        ]);

        $history = ReadingHistory::query()
            ->with('article')
            ->where('user_id', $payload['user_id'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (ReadingHistory $item) => [
                'user_id' => $item->user_id,
                'article_id' => $item->article_id,
                'article_title' => $item->article?->title,
                'is_completed' => $item->is_completed,
                'read_at' => optional($item->read_at)?->toISOString(),
            ]);

        return $this->success($history);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
            'article_id' => ['required', 'integer', 'exists:articles,article_id'],
            'is_completed' => ['sometimes', 'boolean'],
        ]);

        $history = ReadingHistory::query()->updateOrCreate([
            'user_id' => $payload['user_id'],
            'article_id' => $payload['article_id'],
        ], [
            'is_completed' => $payload['is_completed'] ?? false,
            'read_at' => Carbon::now(),
        ]);

        return $this->success([
            'user_id' => $history->user_id,
            'article_id' => $history->article_id,
            'is_completed' => $history->is_completed,
            'read_at' => optional($history->read_at)?->toISOString(),
        ], 'reading history saved', 201);
    }
}
