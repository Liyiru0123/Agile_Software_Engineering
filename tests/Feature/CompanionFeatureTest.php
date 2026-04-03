<?php

namespace Tests\Feature;

use App\Models\CompanionShopItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_companion_page_loads_with_seeded_shop_items(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('companion.index'));

        $response->assertOk()
            ->assertSee('Hiyori Companion')
            ->assertSee('Shop')
            ->assertSee('Sunset Ribbon')
            ->assertSee('Library Cape');
    }

    public function test_daily_login_reward_is_granted_once_per_day(): void
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertRedirect('/');

        $this->assertDatabaseHas('companion_profiles', [
            'user_id' => $user->id,
            'gold' => 25,
            'total_gold_earned' => 25,
        ]);

        $this->assertDatabaseCount('companion_transactions', 1);

        $this->post(route('logout'));

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertRedirect('/');

        $this->assertDatabaseHas('companion_profiles', [
            'user_id' => $user->id,
            'gold' => 25,
            'total_gold_earned' => 25,
        ]);

        $this->assertDatabaseCount('companion_transactions', 1);
    }

    public function test_user_can_buy_an_outfit_when_enough_gold_is_available(): void
    {
        $user = User::factory()->create();
        $item = CompanionShopItem::query()->where('slug', 'sunset-ribbon')->firstOrFail();

        \App\Models\CompanionProfile::query()->create([
            'user_id' => $user->id,
            'gold' => 200,
            'total_gold_earned' => 200,
        ]);

        $this->actingAs($user)
            ->post(route('companion.purchase', $item))
            ->assertRedirect(route('companion.index'));

        $this->assertDatabaseHas('companion_inventories', [
            'user_id' => $user->id,
            'shop_item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('companion_profiles', [
            'user_id' => $user->id,
            'gold' => 80,
            'equipped_shop_item_id' => $item->id,
        ]);
    }
}
