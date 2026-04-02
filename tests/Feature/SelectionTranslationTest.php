<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\LangblyTranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SelectionTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_langbly_translation_service_returns_translated_text(): void
    {
        config([
            'services.langbly.api_key' => 'test-key',
            'services.langbly.base_url' => 'https://api.langbly.com/language/translate/v2',
        ]);

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'translations' => [[
                        'translatedText' => 'research-cn',
                        'detectedSourceLanguage' => 'en',
                    ]],
                ],
            ]),
        ]);

        $result = app(LangblyTranslationService::class)->translate('research', 'zh-CN', 'en');

        $this->assertSame('research-cn', $result['translated_text']);
        $this->assertSame('en', $result['source_language']);
        $this->assertSame('zh-CN', $result['target_language']);
        $this->assertSame('langbly', $result['provider']);
    }

    public function test_selection_save_route_persists_article_paragraph_context(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Selection Save',
            'content' => "First paragraph for testing.\n\nSecond paragraph contains the selected argument.",
            'difficulty' => 2,
            'word_count' => 9,
        ]);

        $response = $this->actingAs($user)->post(route('selection.save'), [
            'article_id' => $article->id,
            'paragraph_index' => 1,
            'selected_text' => 'argument',
            'translated_text' => 'argument-cn',
            'source_language' => 'en',
            'target_language' => 'zh-CN',
        ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('favorite.selected_text', 'argument')
            ->assertJsonPath('favorite.translated_text', 'argument-cn');

        $this->assertDatabaseHas('selection_favorites', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'paragraph_index' => 1,
            'selected_text' => 'argument',
            'translated_text' => 'argument-cn',
            'paragraph_text' => 'Second paragraph contains the selected argument.',
            'target_language' => 'zh-CN',
        ]);
    }

    public function test_article_page_renders_paragraph_metadata_for_selection_tools(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Metadata Demo',
            'content' => "Alpha paragraph.\n\nBeta paragraph.",
            'difficulty' => 1,
            'word_count' => 4,
        ]);

        $response = $this->actingAs($user)->get(route('articles.show', $article));

        $response->assertOk()
            ->assertSee('data-translate-scope="true"', false)
            ->assertSee('data-paragraph-index="0"', false)
            ->assertSee('data-paragraph-index="1"', false);
    }
}
