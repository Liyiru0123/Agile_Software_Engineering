<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\SelectionFavorite;
use App\Services\ArticleTextProcessor;
use App\Services\LangblyTranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class SelectionTranslationController extends Controller
{
    public function __construct(
        protected LangblyTranslationService $translationService,
        protected ArticleTextProcessor $processor
    ) {
    }

    public function translate(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'text' => ['required', 'string', 'min:1', 'max:220'],
            'source_language' => ['sometimes', 'nullable', 'string', 'max:16'],
            'target_language' => ['sometimes', 'nullable', 'string', 'max:16'],
        ]);

        try {
            $result = $this->translationService->translate(
                text: trim($payload['text']),
                targetLanguage: $payload['target_language'] ?? config('services.langbly.default_target', 'zh-CN'),
                sourceLanguage: $payload['source_language'] ?? config('services.langbly.default_source', 'en'),
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }

        return response()->json([
            'translation' => $result,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'article_id' => ['required', 'integer', 'exists:articles,id'],
            'paragraph_index' => ['required', 'integer', 'min:0'],
            'selected_text' => ['required', 'string', 'min:1', 'max:191'],
            'translated_text' => ['sometimes', 'nullable', 'string'],
            'source_language' => ['sometimes', 'nullable', 'string', 'max:16'],
            'target_language' => ['sometimes', 'nullable', 'string', 'max:16'],
        ]);

        $article = Article::query()->findOrFail($payload['article_id']);
        $paragraphs = array_values($this->processor->splitParagraphs($article->content));
        $paragraphIndex = (int) $payload['paragraph_index'];

        if (! array_key_exists($paragraphIndex, $paragraphs)) {
            return response()->json([
                'message' => 'The selected paragraph was not found in this article.',
            ], 422);
        }

        $selectedText = trim((string) $payload['selected_text']);
        $targetLanguage = $payload['target_language'] ?? config('services.langbly.default_target', 'zh-CN');
        $sourceLanguage = $payload['source_language'] ?? config('services.langbly.default_source', 'en');

        $translation = [
            'translated_text' => trim((string) ($payload['translated_text'] ?? '')),
            'source_language' => $sourceLanguage,
            'target_language' => $targetLanguage,
            'provider' => 'langbly',
        ];

        if ($translation['translated_text'] === '') {
            try {
                $translation = $this->translationService->translate(
                    text: $selectedText,
                    targetLanguage: $targetLanguage,
                    sourceLanguage: $sourceLanguage,
                );
            } catch (RuntimeException $exception) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 503);
            }
        }

        $favorite = SelectionFavorite::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'article_id' => $article->id,
                'paragraph_index' => $paragraphIndex,
                'selected_text' => $selectedText,
                'target_language' => $translation['target_language'],
            ],
            [
                'translated_text' => $translation['translated_text'],
                'paragraph_text' => $paragraphs[$paragraphIndex],
                'source_language' => $translation['source_language'],
                'provider' => $translation['provider'],
            ]
        );

        return response()->json([
            'favorite' => [
                'id' => $favorite->id,
                'selected_text' => $favorite->selected_text,
                'translated_text' => $favorite->translated_text,
                'paragraph_index' => $favorite->paragraph_index,
                'article_id' => $favorite->article_id,
                'provider' => $favorite->provider,
            ],
            'message' => $favorite->wasRecentlyCreated
                ? 'Saved to favorites.'
                : 'Already saved. The translation has been refreshed.',
        ]);
    }
}
