<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companion_shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('type')->default('item');
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->string('rarity')->default('common');
            $table->json('visual')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('companion_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->unsignedInteger('gold')->default(0);
            $table->unsignedInteger('total_gold_earned')->default(0);
            $table->foreignId('equipped_shop_item_id')->nullable()->constrained('companion_shop_items')->nullOnDelete();
            $table->timestamp('last_daily_reward_at')->nullable();
            $table->timestamps();
        });

        Schema::create('companion_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_item_id')->constrained('companion_shop_items')->cascadeOnDelete();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'shop_item_id']);
        });

        Schema::create('companion_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('source');
            $table->integer('amount');
            $table->string('reward_key')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'reward_key']);
        });

        DB::table('companion_shop_items')->insert([
            [
                'slug' => 'sunset-ribbon',
                'name' => 'Sunset Ribbon',
                'type' => 'outfit',
                'description' => 'A warm ribbon style inspired by dusk reading sessions.',
                'price' => 120,
                'rarity' => 'starter-plus',
                'visual' => json_encode([
                    'accent' => '#D88C5A',
                    'surface' => '#F5E2D0',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'library-cape',
                'name' => 'Library Cape',
                'type' => 'outfit',
                'description' => 'A bookish layered look for long sessions in the article library.',
                'price' => 180,
                'rarity' => 'rare',
                'visual' => json_encode([
                    'accent' => '#6B3D2E',
                    'surface' => '#E8D6C7',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'study-lantern',
                'name' => 'Study Lantern',
                'type' => 'item',
                'description' => 'A room item that brightens the study corner with a softer glow.',
                'price' => 90,
                'rarity' => 'common',
                'visual' => json_encode([
                    'accent' => '#C9A961',
                    'surface' => '#FFF5DB',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'storybook-badge',
                'name' => 'Storybook Badge',
                'type' => 'item',
                'description' => 'A collectible keepsake awarded to readers who like saving excerpts.',
                'price' => 60,
                'rarity' => 'common',
                'visual' => json_encode([
                    'accent' => '#8B6B47',
                    'surface' => '#F9EFE2',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('companion_transactions');
        Schema::dropIfExists('companion_inventories');
        Schema::dropIfExists('companion_profiles');
        Schema::dropIfExists('companion_shop_items');
    }
};
