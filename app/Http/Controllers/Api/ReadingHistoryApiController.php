<?php

namespace App\Http\Controllers\Api;

use App\Models\ReadingHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingHistoryApiController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
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
                'last_page' => $item->last_page,
                'page_label' => $item->page_label,
                'continue_url' => $item->continue_url,
                'read_at' => optional($item->last_viewed_at)?->toISOString(),
            ]);

        return $this->success($history);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'article_id' => ['required', 'integer', 'exists:articles,id'],
            'is_completed' => ['sometimes', 'boolean'],
            'last_page' => ['sometimes', 'string', 'in:article,listening,speaking,reading,writing'],
        ]);

        $history = ReadingHistory::query()->firstOrNew([
            'user_id' => $payload['user_id'],
            'article_id' => $payload['article_id'],
        ]);

        $history->is_completed = $payload['is_completed'] ?? $history->is_completed ?? false;
        $history->last_page = $payload['last_page'] ?? $history->last_page ?? 'article';
        $history->visit_count = $history->exists ? ((int) $history->visit_count + 1) : 1;
        $history->first_viewed_at ??= now();
        $history->last_viewed_at = now();
        $history->save();

        return $this->success([
            'user_id' => $history->user_id,
            'article_id' => $history->article_id,
            'is_completed' => $history->is_completed,
            'last_page' => $history->last_page,
            'page_label' => $history->page_label,
            'continue_url' => $history->continue_url,
            'read_at' => optional($history->last_viewed_at)?->toISOString(),
        ], 'reading history saved', 201);
    }
}
