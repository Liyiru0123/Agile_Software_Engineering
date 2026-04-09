<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_home_page_prioritizes_plan_sections(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Dashboard Planning Article',
            'content' => 'A short article used to verify the dashboard layout.',
            'difficulty' => 2,
            'word_count' => 10,
        ]);

        UserPlan::query()->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'plan_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Plan Calendar')
            ->assertSee("Today's Tasks")
            ->assertSee('Quick Add Plan')
            ->assertSee('Dashboard Planning Article')
            ->assertSee('Analysis')
            ->assertSee('Community');
    }

    public function test_dashboard_quick_add_plan_can_create_a_skill_target(): void
    {
        $user = User::factory()->create();

        $date = now()->toDateString();

        $this->actingAs($user)
            ->post(route('plans.store'), [
                'plan_date' => $date,
                'plan_kind' => 'skill',
                'skill_type' => 'listening',
                'target_count' => 3,
            ])
            ->assertRedirect(route('home', [
                'date' => $date,
                'month' => now()->format('Y-m'),
            ]));

        $this->assertDatabaseHas('user_plans', [
            'user_id' => $user->id,
            'plan_kind' => 'skill',
            'plan_date' => $date.' 00:00:00',
            'skill_type' => 'listening',
            'target_count' => 3,
            'title' => 'Listening practice x3',
            'status' => 'pending',
        ]);
    }

    public function test_dashboard_quick_add_plan_can_create_a_custom_task(): void
    {
        $user = User::factory()->create();
        $date = now()->toDateString();

        $this->actingAs($user)
            ->post(route('plans.store'), [
                'plan_date' => $date,
                'plan_kind' => 'custom',
                'custom_title' => 'Shadow two speaking answers',
            ])
            ->assertRedirect(route('home', [
                'date' => $date,
                'month' => now()->format('Y-m'),
            ]));

        $this->assertDatabaseHas('user_plans', [
            'user_id' => $user->id,
            'plan_kind' => 'custom',
            'plan_date' => $date.' 00:00:00',
            'title' => 'Shadow two speaking answers',
            'status' => 'pending',
        ]);
    }

    public function test_dashboard_plan_status_can_be_updated_via_json_request(): void
    {
        $user = User::factory()->create();
        $plan = UserPlan::query()->create([
            'user_id' => $user->id,
            'article_id' => null,
            'plan_date' => now()->toDateString(),
            'plan_kind' => 'custom',
            'title' => 'Review vocabulary notebook',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->patchJson(route('plans.update', $plan), [
                'status' => 'completed',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('plan.status', 'completed');

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
            'status' => 'completed',
        ]);

        $this->assertNotNull($plan->fresh()->completed_at);

        $this->actingAs($user)
            ->get(route('home', [
                'date' => now()->toDateString(),
                'month' => now()->format('Y-m'),
            ]))
            ->assertOk()
            ->assertSee('1/1 done')
            ->assertSee('All done')
            ->assertDontSee('1 pending');
    }

    public function test_dashboard_plan_delete_rejects_non_owner(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $plan = UserPlan::query()->create([
            'user_id' => $owner->id,
            'article_id' => null,
            'plan_date' => now()->toDateString(),
            'plan_kind' => 'custom',
            'title' => 'Private study task',
            'status' => 'pending',
        ]);

        $this->actingAs($intruder)
            ->deleteJson(route('plans.destroy', $plan))
            ->assertForbidden()
            ->assertJsonPath('error', 'Unauthorized');

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
        ]);
    }
}
