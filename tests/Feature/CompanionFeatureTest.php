<?php

namespace Tests\Feature;

use App\Models\CompanionShopItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_page_loads_with_seeded_shop_items(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('shop.index'));

        $response->assertOk()
            ->assertSee('Shop')
            ->assertSee('Titles')
            ->assertSee('One-Day Check-In')
            ->assertSee('Speed Listener')
            ->assertSee('7-Day Check-In Streak')
            ->assertSee('Makeup Check-In Card')
            ->assertSee('Sunset Ribbon');
    }

    public function test_daily_check_in_is_granted_once_per_day(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('shop.index'))
            ->post(route('shop.check-in'))
            ->assertRedirect(route('shop.index'));

        $this->assertDatabaseCount('companion_transactions', 1);

        $this->assertDatabaseHas('companion_profiles', [
            'user_id' => $user->id,
            'gold' => 25,
            'total_gold_earned' => 25,
        ]);

        $this->actingAs($user)
            ->from(route('shop.index'))
            ->post(route('shop.check-in'))
            ->assertRedirect(route('shop.index'));

        $this->assertDatabaseHas('companion_profiles', [
            'user_id' => $user->id,
            'gold' => 25,
            'total_gold_earned' => 25,
        ]);

        $this->assertDatabaseCount('daily_attendances', 1);
        $this->assertDatabaseCount('companion_transactions', 1);
    }

    public function test_user_can_buy_stackable_makeup_cards_multiple_times(): void
    {
        $user = User::factory()->create();
        $item = CompanionShopItem::query()->where('slug', 'makeup-checkin-card')->firstOrFail();

        \App\Models\CompanionProfile::query()->create([
            'user_id' => $user->id,
            'gold' => 300,
            'total_gold_earned' => 300,
        ]);

        $this->actingAs($user)
            ->from(route('shop.index'))
            ->post(route('shop.purchase', $item))
            ->assertRedirect(route('shop.index'));

        $this->actingAs($user)
            ->from(route('shop.index'))
            ->post(route('shop.purchase', $item))
            ->assertRedirect(route('shop.index'));

        $this->assertDatabaseHas('companion_inventories', [
            'user_id' => $user->id,
            'shop_item_id' => $item->id,
            'quantity' => 2,
        ]);
    }

    public function test_user_can_use_makeup_card_for_latest_missed_day(): void
    {
        Carbon::setTestNow('2026-04-09 10:00:00');

        $user = User::factory()->create();
        $item = CompanionShopItem::query()->where('slug', 'makeup-checkin-card')->firstOrFail();

        \App\Models\CompanionProfile::query()->create([
            'user_id' => $user->id,
            'gold' => 200,
            'total_gold_earned' => 200,
        ]);

        \App\Models\CompanionInventory::query()->create([
            'user_id' => $user->id,
            'shop_item_id' => $item->id,
            'quantity' => 1,
            'purchased_at' => now(),
        ]);

        \App\Models\DailyAttendance::query()->create([
            'user_id' => $user->id,
            'attendance_date' => '2026-04-07',
            'source' => 'claim',
            'reward_amount' => 25,
        ]);

        $this->actingAs($user)
            ->from(route('shop.index'))
            ->post(route('shop.check-in.makeup'))
            ->assertRedirect(route('shop.index'));

        $this->assertDatabaseHas('daily_attendances', [
            'user_id' => $user->id,
            'attendance_date' => '2026-04-08 00:00:00',
            'source' => 'makeup',
            'shop_item_id' => $item->id,
        ]);

        $this->assertDatabaseMissing('companion_inventories', [
            'user_id' => $user->id,
            'shop_item_id' => $item->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_user_can_unequip_badge_style(): void
    {
        $user = User::factory()->create();
        $badge = CompanionShopItem::query()->where('slug', 'storybook-badge')->firstOrFail();

        \App\Models\CompanionProfile::query()->create([
            'user_id' => $user->id,
            'gold' => 0,
            'total_gold_earned' => 0,
            'equipped_shop_item_id' => $badge->id,
        ]);

        \App\Models\CompanionInventory::query()->create([
            'user_id' => $user->id,
            'shop_item_id' => $badge->id,
            'quantity' => 1,
            'purchased_at' => now(),
        ]);

        $this->actingAs($user)
            ->from(route('shop.index'))
            ->post(route('shop.unequip'))
            ->assertRedirect(route('shop.index'));

        $this->assertDatabaseHas('companion_profiles', [
            'user_id' => $user->id,
            'equipped_shop_item_id' => null,
        ]);
    }
}

