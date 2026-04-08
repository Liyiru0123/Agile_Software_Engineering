<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ReadingHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_visit_creates_or_updates_reading_history(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Design',
            'content' => 'Bridge design balances safety and efficiency.',
            'difficulty' => 1,
            'word_count' => 6,
        ]);

        $this->actingAs($user)->get(route('articles.show', $article))->assertOk();

        $this->assertDatabaseHas('reading_history', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'last_page' => 'article',
            'visit_count' => 1,
        ]);

        $this->actingAs($user)->get(route('articles.reading', $article))->assertOk();

        $this->assertDatabaseHas('reading_history', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'last_page' => 'reading',
            'visit_count' => 2,
        ]);
    }

    public function test_history_page_lists_recent_records(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Tunnel Engineering',
            'content' => 'Tunnel projects require risk management.',
            'difficulty' => 2,
            'word_count' => 5,
        ]);

        ReadingHistory::query()->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'last_page' => 'listening',
            'visit_count' => 3,
            'first_viewed_at' => now()->subDay(),
            'last_viewed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('history.index'));

        $response->assertOk()
            ->assertSee('Browsing History')
            ->assertSee('Tunnel Engineering')
            ->assertSee('Listening')
            ->assertSee('3 visits');
    }

    public function test_continue_route_redirects_to_latest_history_page(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Acoustic Materials',
            'content' => 'Acoustic materials affect speech clarity.',
            'difficulty' => 2,
            'word_count' => 5,
        ]);

        ReadingHistory::query()->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'last_page' => 'speaking',
            'visit_count' => 1,
            'first_viewed_at' => now()->subMinutes(10),
            'last_viewed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('history.continue'))
            ->assertRedirect(route('articles.speaking', $article));
    }

    public function test_continue_route_falls_back_to_article_index_when_history_is_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('history.continue'))
            ->assertRedirect(route('articles.index'));
    }

    public function test_dashboard_alias_redirects_to_home(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('home'));
    }
}
