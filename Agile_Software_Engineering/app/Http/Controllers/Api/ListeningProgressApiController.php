<?php

namespace App\Http\Controllers\Api;

use App\Models\ListeningProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListeningProgressApiController extends ApiController
{
    public function show(Request $request, int $articleId): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
        ]);

        $progress = ListeningProgress::query()
            ->where('user_id', $payload['user_id'])
            ->where('article_id', $articleId)
            ->first();

        return $this->success($progress ? [
            'user_id' => $progress->user_id,
            'article_id' => $progress->article_id,
            'last_position' => $progress->last_position,
            'playback_speed' => $progress->playback_speed,
            'is_completed' => $progress->is_completed,
            'listen_count' => $progress->listen_count,
            'updated_at' => optional($progress->updated_at)?->toISOString(),
        ] : null);
    }

    public function store(Request $request, int $articleId): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
            'last_position' => ['sometimes', 'integer', 'min:0'],
            'playback_speed' => ['sometimes', 'numeric', 'min:0.5', 'max:2'],
            'is_completed' => ['sometimes', 'boolean'],
            'listen_count' => ['sometimes', 'integer', 'min:0'],
        ]);

        $progress = ListeningProgress::query()->updateOrCreate([
            'user_id' => $payload['user_id'],
            'article_id' => $articleId,
        ], [
            'last_position' => $payload['last_position'] ?? 0,
            'playback_speed' => $payload['playback_speed'] ?? 1,
            'is_completed' => $payload['is_completed'] ?? false,
            'listen_count' => $payload['listen_count'] ?? 0,
            'updated_at' => now(),
        ]);

        return $this->success([
            'user_id' => $progress->user_id,
            'article_id' => $progress->article_id,
            'last_position' => $progress->last_position,
            'playback_speed' => $progress->playback_speed,
            'is_completed' => $progress->is_completed,
            'listen_count' => $progress->listen_count,
            'updated_at' => optional($progress->updated_at)?->toISOString(),
        ], 'listening progress saved', 201);
    }
}
