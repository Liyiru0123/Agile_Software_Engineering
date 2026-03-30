<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTrainingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_detail_page_loads_with_training_cards(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Safety Basics',
            'content' => 'Bridges must carry weight safely. Engineers inspect them often. Maintenance prevents major failures.',
            'difficulty' => 2,
            'word_count' => 12,
        ]);

        $response = $this->actingAs($user)->get(route('articles.show', $article));

        $response->assertOk()
            ->assertSee('Choose a skill to train')
            ->assertSee('Listening')
            ->assertSee('Writing');
    }

    public function test_listening_generation_works_without_ai_keys(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Research Methodology',
            'content' => 'Research methodology is the backbone of any academic study. However, researchers must choose methods carefully. Therefore, strong evidence supports mixed methods in some contexts.',
            'difficulty' => 2,
            'word_count' => 24,
        ]);

        $response = $this->actingAs($user)->get(route('articles.listening', $article));

        $response->assertOk()
            ->assertSee('Listen to the audio and type the missing words directly into the passage.')
            ->assertSee('Complete')
            ->assertSee('Fallback-generated');
    }
}
