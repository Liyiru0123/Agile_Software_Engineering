<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $items = [
            [
                'slug' => 'focus-streak-badge',
                'name' => 'Focus Streak Badge',
                'type' => 'item',
                'stackable' => false,
                'description' => 'A calm badge for learners who keep a steady daily study rhythm.',
                'price' => 70,
                'rarity' => 'common',
                'benefit_key' => null,
                'visual' => json_encode([
                    'accent' => '#7F8F5A',
                    'surface' => '#EEF3DE',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'listening-scout-badge',
                'name' => 'Listening Scout Badge',
                'type' => 'item',
                'stackable' => false,
                'description' => 'A collectible badge themed around careful listening and detail catching.',
                'price' => 85,
                'rarity' => 'common',
                'benefit_key' => null,
                'visual' => json_encode([
                    'accent' => '#5F7FA8',
                    'surface' => '#E6EEF8',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'reading-marathon-badge',
                'name' => 'Reading Marathon Badge',
                'type' => 'item',
                'stackable' => false,
                'description' => 'A long-session reading keepsake for learners who finish full article cycles.',
                'price' => 110,
                'rarity' => 'starter-plus',
                'benefit_key' => null,
                'visual' => json_encode([
                    'accent' => '#A56B4D',
                    'surface' => '#F7E8DA',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'writing-craft-badge',
                'name' => 'Writing Craft Badge',
                'type' => 'item',
                'stackable' => false,
                'description' => 'A badge for learners who revise writing responses with care and structure.',
                'price' => 120,
                'rarity' => 'starter-plus',
                'benefit_key' => null,
                'visual' => json_encode([
                    'accent' => '#8A5E8C',
                    'surface' => '#F2E4F3',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'forum-heart-badge',
                'name' => 'Forum Heart Badge',
                'type' => 'item',
                'stackable' => false,
                'description' => 'A social keepsake for active learners who share and support in forum threads.',
                'price' => 135,
                'rarity' => 'rare',
                'benefit_key' => null,
                'visual' => json_encode([
                    'accent' => '#C95F43',
                    'surface' => '#FBE4DB',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'night-owl-badge',
                'name' => 'Night Owl Badge',
                'type' => 'item',
                'stackable' => false,
                'description' => 'A twilight-themed collectible for learners who often finish sessions late.',
                'price' => 95,
                'rarity' => 'common',
                'benefit_key' => null,
                'visual' => json_encode([
                    'accent' => '#5A567A',
                    'surface' => '#E9E7F4',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($items as $item) {
            if (! DB::table('companion_shop_items')->where('slug', $item['slug'])->exists()) {
                DB::table('companion_shop_items')->insert($item);
            }
        }
    }

    public function down(): void
    {
        DB::table('companion_shop_items')
            ->whereIn('slug', [
                'focus-streak-badge',
                'listening-scout-badge',
                'reading-marathon-badge',
                'writing-craft-badge',
                'forum-heart-badge',
                'night-owl-badge',
            ])
            ->delete();
    }
};
