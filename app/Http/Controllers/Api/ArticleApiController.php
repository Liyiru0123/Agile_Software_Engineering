<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ArticleTextProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleApiController extends Controller
{
    public function __construct(
        protected ArticleTextProcessor $processor
    ) {
    }

    public function index(): JsonResponse
    {
        $articles = Article::query()
            ->latest('id')
            ->get()
            ->map(fn (Article $article) => $this->formatArticleSummary($article))
            ->values();

        return $this->success($articles);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate($this->rules());

        $article = DB::transaction(function () use ($payload, $request) {
            $storedAudio = $request->hasFile('audio_file')
                ? $request->file('audio_file')->store('articles/audio', 'public')
                : null;

            $content = trim($payload['content']);

            $article = Article::query()->create([
                'subject' => $payload['subject'],
                'title' => $payload['title'],
                'slug' => $this->generateUniqueSlug($payload['title']),
                'author' => $payload['author'] ?? null,
                'source' => $payload['source'] ?? null,
                'level' => $payload['level'],
                'content' => $content,
                'resource_type' => $payload['resource_type'] ?? ($storedAudio ? 'audio' : 'text'),
                'accent' => $payload['accent'] ?? 'US',
                'audio_url' => $storedAudio,
                'video_url' => $payload['video_url'] ?? null,
                'word_count' => $this->processor->countWords($content),
                'total_duration' => $payload['total_duration'] ?? 0,
            ]);

            $article->segments()->createMany($this->processor->buildSegments($content));

            return $article->load('segments');
        });

        return $this->success([
            'article' => $this->formatArticleDetail($article),
            'reading' => $this->buildReadingPayload($article),
        ], 'article created', 201);
    }

    public function show(Article $article): JsonResponse
    {
        $article->loadCount('segments');

        return $this->success($this->formatArticleDetail($article));
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $payload = $request->validate($this->rules(false));

        $article = DB::transaction(function () use ($article, $payload, $request) {
            $updates = [];

            foreach (['subject', 'title', 'author', 'source', 'level', 'resource_type', 'accent', 'total_duration', 'video_url'] as $field) {
                if (array_key_exists($field, $payload)) {
                    $updates[$field] = $payload[$field];
                }
            }

            if (array_key_exists('title', $payload) && $payload['title'] !== $article->title) {
                $updates['slug'] = $this->generateUniqueSlug($payload['title'], $article->getKey());
            }

            if (array_key_exists('content', $payload)) {
                $content = trim($payload['content']);
                $updates['content'] = $content;
                $updates['word_count'] = $this->processor->countWords($content);
            }

            if ($request->hasFile('audio_file')) {
                $this->deleteAudioFile($article->getRawOriginal('audio_url'));
                $updates['audio_url'] = $request->file('audio_file')->store('articles/audio', 'public');
            }

            $article->update($updates);

            if (array_key_exists('content', $payload)) {
                $article->segments()->delete();
                $article->segments()->createMany($this->processor->buildSegments($content));
            }

            return $article->fresh()->load('segments');
        });

        return $this->success([
            'article' => $this->formatArticleDetail($article),
            'reading' => $this->buildReadingPayload($article),
        ], 'article updated');
    }

    public function destroy(Article $article): JsonResponse
    {
        DB::transaction(function () use ($article) {
            $this->deleteAudioFile($article->getRawOriginal('audio_url'));
            $article->segments()->delete();
            $article->delete();
        });

        return $this->success(null, 'article deleted');
    }

    public function reading(Article $article): JsonResponse
    {
        $article->load('segments');

        return $this->success($this->buildReadingPayload($article));
    }

    public function audio(Article $article): JsonResponse
    {
        return $this->success([
            'article_id' => $article->id,
            'title' => $article->title,
            'has_audio' => $article->has_audio,
            'audio_url' => $article->audio_url,
            'accent' => $article->accent,
            'total_duration' => $article->total_duration,
        ]);
    }

    private function rules(bool $isCreate = true): array
    {
        return [
            'subject' => $isCreate
                ? ['required', Rule::in($this->subjects())]
                : ['sometimes', Rule::in($this->subjects())],
            'title' => $isCreate ? ['required', 'string', 'max:255'] : ['sometimes', 'string', 'max:255'],
            'author' => ['sometimes', 'nullable', 'string', 'max:100'],
            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'level' => $isCreate
                ? ['required', Rule::in(['Easy', 'Intermediate', 'Advanced'])]
                : ['sometimes', Rule::in(['Easy', 'Intermediate', 'Advanced'])],
            'content' => $isCreate ? ['required', 'string'] : ['sometimes', 'string'],
            'resource_type' => ['sometimes', 'nullable', Rule::in(['text', 'audio', 'video'])],
            'accent' => ['sometimes', 'nullable', Rule::in(['US', 'UK'])],
            'video_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'total_duration' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'audio_file' => ['sometimes', 'nullable', 'file', 'mimes:mp3,wav,m4a,aac,ogg', 'max:51200'],
        ];
    }

    private function subjects(): array
    {
        return [
            'Civil Engineering',
            'Mathematics',
            'Computer Science',
            'Mechanical Engineering',
            'Mechanical Engineering with Transportation',
        ];
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $base = $base !== '' ? $base : 'article';
        $slug = $base;
        $counter = 2;

        while (
            Article::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function formatArticleSummary(Article $article): array
    {
        return [
            'id' => $article->id,
            'subject' => $article->subject,
            'title' => $article->title,
            'slug' => $article->slug,
            'author' => $article->author,
            'source' => $article->source,
            'level' => $article->level,
            'resource_type' => $article->resource_type,
            'word_count' => $article->word_count,
            'has_audio' => $article->has_audio,
            'created_at' => optional($article->created_at)?->toISOString(),
        ];
    }

    private function formatArticleDetail(Article $article): array
    {
        return [
            'id' => $article->id,
            'subject' => $article->subject,
            'title' => $article->title,
            'slug' => $article->slug,
            'author' => $article->author,
            'source' => $article->source,
            'level' => $article->level,
            'content' => $article->content,
            'resource_type' => $article->resource_type,
            'accent' => $article->accent,
            'word_count' => $article->word_count,
            'total_duration' => $article->total_duration,
            'audio_url' => $article->audio_url,
            'video_url' => $article->video_url,
            'has_audio' => $article->has_audio,
            'segment_count' => $article->relationLoaded('segments')
                ? $article->segments->count()
                : ($article->segments_count ?? $article->segments()->count()),
            'created_at' => optional($article->created_at)?->toISOString(),
            'updated_at' => optional($article->updated_at)?->toISOString(),
        ];
    }

    private function buildReadingPayload(Article $article): array
    {
        $segments = $article->relationLoaded('segments')
            ? $article->segments
            : $article->segments()->get();

        $paragraphs = $segments
            ->groupBy('paragraph_index')
            ->map(function ($group, $paragraphIndex) {
                $sentences = $group->map(fn ($segment) => [
                    'id' => $segment->id,
                    'sentence_index' => $segment->sentence_index,
                    'text' => $segment->content_en,
                    'translation' => $segment->content_cn,
                    'start_time' => $segment->start_time,
                    'end_time' => $segment->end_time,
                ])->values();

                return [
                    'paragraph_index' => (int) $paragraphIndex,
                    'text' => $sentences->pluck('text')->implode(' '),
                    'sentences' => $sentences,
                ];
            })
            ->values();

        return [
            'article_id' => $article->id,
            'title' => $article->title,
            'audio_url' => $article->audio_url,
            'paragraphs' => $paragraphs,
        ];
    }

    private function deleteAudioFile(?string $storedAudio): void
    {
        if (! $storedAudio || Str::startsWith($storedAudio, ['http://', 'https://'])) {
            return;
        }

        $path = Str::startsWith($storedAudio, '/storage/')
            ? Str::after($storedAudio, '/storage/')
            : $storedAudio;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function success(mixed $data = null, string $message = 'success', int $status = 200): JsonResponse
    {
        return response()->json([
            'code' => 0,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}

