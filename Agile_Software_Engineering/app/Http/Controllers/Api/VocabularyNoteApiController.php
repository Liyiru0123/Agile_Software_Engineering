<?php

namespace App\Http\Controllers\Api;

use App\Models\VocabularyNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VocabularyNoteApiController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
        ]);

        $notes = VocabularyNote::query()
            ->where('user_id', $payload['user_id'])
            ->orderByDesc('vocabulary_note_id')
            ->get()
            ->map(fn (VocabularyNote $note) => [
                'id' => $note->id,
                'user_id' => $note->user_id,
                'word' => $note->word,
                'definition' => $note->definition,
                'example' => $note->example,
            ]);

        return $this->success($notes);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
            'word' => ['required', 'string', 'max:100'],
            'definition' => ['required', 'string'],
            'example' => ['sometimes', 'nullable', 'string'],
        ]);

        $note = VocabularyNote::query()->create($payload);

        return $this->success([
            'id' => $note->id,
            'user_id' => $note->user_id,
            'word' => $note->word,
            'definition' => $note->definition,
            'example' => $note->example,
        ], 'vocabulary note created', 201);
    }
}
