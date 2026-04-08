<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Live2dConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_live2d_conversation_page_loads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('speaking.live2d'))
            ->assertOk()
            ->assertSee('Live2D AI Conversation')
            ->assertSee('Subtitle')
            ->assertSee('Start Voice');
    }

    public function test_live2d_interface_rejects_empty_message(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('speaking.live2d.interface'), [
                'message' => '   ',
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Please type something or use the microphone before sending.');
    }

    public function test_live2d_interface_returns_low_cost_fallback_reply_when_api_key_is_missing(): void
    {
        config(['services.gemini.api_key' => '']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('speaking.live2d.interface'), [
                'message' => 'Hello Hiyori',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('provider', 'local-fallback')
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('history_count', 2);

        $this->assertSame(2, count(session('live2d_ai_dialogue.history', [])));
    }

    public function test_live2d_interface_can_clear_session_history(): void
    {
        config(['services.gemini.api_key' => '']);
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('speaking.live2d.interface'), [
            'message' => 'Help me with speaking',
        ])->assertOk();

        $this->actingAs($user)
            ->postJson(route('speaking.live2d.interface'), [
                'reset' => true,
            ])
            ->assertOk()
            ->assertJsonPath('history_count', 0)
            ->assertJsonPath('provider', 'system');

        $this->assertSame([], session('live2d_ai_dialogue.history', []));
    }
}
