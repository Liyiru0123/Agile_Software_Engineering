<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\LangblyTranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelectionTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_translate_endpoint_returns_langbly_translation(): void
    {
        $this->mock(LangblyTranslationService::class, function ($mock) {
            $mock->shouldReceive('translate')
                ->once()
                ->with('bridge safety', 'zh-CN', 'en')
                ->andReturn([
                    'translated_text' => 'bridge safety translated',
                    'source_language' => 'en',
                    'target_language' => 'zh-CN',
                    'provider' => 'langbly',
                ]);
        });

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('selection.translate'), [
            'text' => 'bridge safety',
            'source_language' => 'en',
            'target_language' => 'zh-CN',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertOk()
            ->assertJsonPath('translation.translated_text', 'bridge safety translated')
            ->assertJsonPath('translation.provider', 'langbly');
    }

    public function test_translate_endpoint_returns_clear_message_for_selection_that_is_too_long(): void
    {
        $user = User::factory()->create();
        $longText = str_repeat('a', 221);

        $response = $this->actingAs($user)->post(route('selection.translate'), [
            'text' => $longText,
            'source_language' => 'en',
            'target_language' => 'zh-CN',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Selected text is too long to translate at once. Please keep it within 220 characters.');
    }

    public function test_save_endpoint_persists_selection_favorite(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Safety',
            'content' => "Bridge safety depends on regular inspection.\n\nMaintenance teams record each repair.",
            'difficulty' => 2,
            'word_count' => 10,
        ]);

        $response = $this->actingAs($user)->post(route('selection.save'), [
            'article_id' => $article->id,
            'paragraph_index' => 0,
            'selected_text' => 'regular inspection',
            'translated_text' => 'regular inspection translated',
            'source_language' => 'en',
            'target_language' => 'zh-CN',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertOk()
            ->assertJsonPath('favorite.article_id', $article->id)
            ->assertJsonPath('favorite.paragraph_index', 0)
            ->assertJsonPath('favorite.translated_text', 'regular inspection translated');

        $this->assertDatabaseHas('selection_favorites', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'paragraph_index' => 0,
            'selected_text' => 'regular inspection',
            'translated_text' => 'regular inspection translated',
        ]);
    }

    public function test_save_endpoint_reuses_existing_selection_and_refreshes_message(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Safety',
            'content' => "Bridge safety depends on regular inspection.\n\nMaintenance teams record each repair.",
            'difficulty' => 2,
            'word_count' => 10,
        ]);

        $this->actingAs($user)->postJson(route('selection.save'), [
            'article_id' => $article->id,
            'paragraph_index' => 0,
            'selected_text' => 'regular inspection',
            'translated_text' => 'first translation',
            'source_language' => 'en',
            'target_language' => 'zh-CN',
        ])->assertOk();

        $this->actingAs($user)->postJson(route('selection.save'), [
            'article_id' => $article->id,
            'paragraph_index' => 0,
            'selected_text' => 'regular inspection',
            'translated_text' => 'updated translation',
            'source_language' => 'en',
            'target_language' => 'zh-CN',
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Already saved. The translation has been refreshed.')
            ->assertJsonPath('favorite.translated_text', 'updated translation');

        $this->assertDatabaseCount('selection_favorites', 1);
    }

    public function test_save_endpoint_rejects_missing_paragraph_index(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Academic Writing',
            'content' => 'Only one paragraph exists here.',
            'difficulty' => 1,
            'word_count' => 5,
        ]);

        $this->actingAs($user)
            ->postJson(route('selection.save'), [
                'article_id' => $article->id,
                'paragraph_index' => 2,
                'selected_text' => 'paragraph',
                'translated_text' => '段落',
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'The selected paragraph was not found in this article.');
    }

    public function test_article_page_includes_translation_metadata_for_paragraphs(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Academic Writing',
            'content' => "First paragraph text.\n\nSecond paragraph text.",
            'difficulty' => 1,
            'word_count' => 6,
        ]);

        $response = $this->actingAs($user)->get(route('articles.show', $article));

        $response->assertOk()
            ->assertSee('data-translate-scope="true"', false)
            ->assertSee('data-paragraph-index="0"', false)
            ->assertSee('data-paragraph-index="1"', false);
    }
}
