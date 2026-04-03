<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\SelectionFavorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotebookPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_notebook_index_lists_saved_excerpt_with_source_article(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Materials',
            'content' => 'Steel and concrete must be selected carefully.',
            'difficulty' => 1,
            'word_count' => 7,
        ]);

        SelectionFavorite::query()->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'paragraph_index' => 0,
            'selected_text' => 'selected sentence',
            'translated_text' => 'translated sentence',
            'paragraph_text' => 'Steel and concrete must be selected carefully.',
            'source_language' => 'en',
            'target_language' => 'zh-CN',
            'provider' => 'langbly',
        ]);

        $response = $this->actingAs($user)->get(route('notebook.index'));

        $response->assertOk()
            ->assertSee('Notebook')
            ->assertSee('selected sentence')
            ->assertSee('Bridge Materials')
            ->assertSee('translated sentence');
    }

    public function test_notebook_review_page_shows_saved_excerpt_and_article_title(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Tunnel Safety',
            'content' => 'Ventilation systems protect tunnel users.',
            'difficulty' => 2,
            'word_count' => 5,
        ]);

        SelectionFavorite::query()->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'paragraph_index' => 0,
            'selected_text' => 'Ventilation systems',
            'translated_text' => 'ventilation systems translated',
            'paragraph_text' => 'Ventilation systems protect tunnel users.',
            'source_language' => 'en',
            'target_language' => 'zh-CN',
            'provider' => 'langbly',
        ]);

        $response = $this->actingAs($user)->get(route('notebook.review'));

        $response->assertOk()
            ->assertSee('Notebook Review')
            ->assertSee('Ventilation systems')
            ->assertSee('ventilation systems translated')
            ->assertSee('Tunnel Safety');
    }
}
