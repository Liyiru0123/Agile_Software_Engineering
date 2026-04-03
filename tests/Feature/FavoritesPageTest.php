<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FavoritesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorites_index_lists_saved_articles(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Failure Prevention',
            'content' => 'Engineers inspect bridge joints and cables regularly.',
            'difficulty' => 2,
            'word_count' => 7,
        ]);

        DB::table('user_favorites')->insert([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertOk()
            ->assertSee('Favorites')
            ->assertSee('Bridge Failure Prevention')
            ->assertSee('Open Article');
    }

    public function test_favorites_plan_page_loads_for_saved_articles(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Tunnel Ventilation',
            'content' => 'Ventilation keeps tunnels safe during heavy use.',
            'difficulty' => 1,
            'word_count' => 8,
        ]);

        DB::table('user_favorites')->insert([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('favorites.plan'));

        $response->assertOk()
            ->assertSee('Generate Plan from Favorites')
            ->assertSee('Tunnel Ventilation')
            ->assertSee('Create Study Plan');
    }
}
