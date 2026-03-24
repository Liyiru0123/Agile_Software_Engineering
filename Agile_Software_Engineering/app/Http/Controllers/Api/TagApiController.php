<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagApiController extends ApiController
{
    public function index(): JsonResponse
    {
        $tags = Tag::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]);

        return $this->success($tags);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:60', Rule::unique('tags', 'slug')],
        ]);

        $tag = Tag::query()->create([
            'name' => $payload['name'],
            'slug' => $payload['slug'] ?? Str::slug($payload['name']),
        ]);

        return $this->success([
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
        ], 'tag created', 201);
    }
}
