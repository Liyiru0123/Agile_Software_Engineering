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

    public function test_favorites_plan_submission_creates_article_plans_only_for_selected_favorites(): void
    {
        $user = User::factory()->create();
        $favoriteArticle = Article::query()->create([
            'title' => 'Saved Article',
            'content' => 'Saved article content for planning.',
            'difficulty' => 1,
            'word_count' => 5,
        ]);
        $otherArticle = Article::query()->create([
            'title' => 'Unsaved Article',
            'content' => 'This article is not in favorites.',
            'difficulty' => 1,
            'word_count' => 6,
        ]);

        DB::table('user_favorites')->insert([
            'user_id' => $user->id,
            'article_id' => $favoriteArticle->id,
            'created_at' => now(),
        ]);

        $planDate = now()->addDay()->toDateString();

        $this->actingAs($user)
            ->post(route('favorites.plan.store'), [
                'article_ids' => [$favoriteArticle->id, $otherArticle->id],
                'plan_date' => $planDate,
            ])
            ->assertRedirect(route('favorites.plan'));

        $this->assertDatabaseHas('user_plans', [
            'user_id' => $user->id,
            'article_id' => $favoriteArticle->id,
            'plan_kind' => 'article',
            'plan_date' => $planDate.' 00:00:00',
            'status' => 'pending',
        ]);

        $this->assertDatabaseMissing('user_plans', [
            'user_id' => $user->id,
            'article_id' => $otherArticle->id,
            'plan_date' => $planDate.' 00:00:00',
        ]);
    }
}
