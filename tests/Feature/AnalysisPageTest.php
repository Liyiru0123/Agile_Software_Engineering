<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_analysis_page_loads_without_missing_dashboard_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('study.analysis'))
            ->assertOk()
            ->assertSee('Learning Analysis')
            ->assertSee('Listening Weak Spots')
            ->assertSee('Speaking Weak Spots');
    }
}
