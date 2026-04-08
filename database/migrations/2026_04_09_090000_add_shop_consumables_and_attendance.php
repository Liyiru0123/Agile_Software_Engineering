<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companion_shop_items')) {
            Schema::table('companion_shop_items', function (Blueprint $table) {
                if (! Schema::hasColumn('companion_shop_items', 'stackable')) {
                    $table->boolean('stackable')->default(false)->after('type');
                }

                if (! Schema::hasColumn('companion_shop_items', 'benefit_key')) {
                    $table->string('benefit_key')->nullable()->after('rarity');
                }
            });
        }

        if (Schema::hasTable('companion_inventories')) {
            Schema::table('companion_inventories', function (Blueprint $table) {
                if (! Schema::hasColumn('companion_inventories', 'quantity')) {
                    $table->unsignedInteger('quantity')->default(1)->after('shop_item_id');
                }

                if (! Schema::hasColumn('companion_inventories', 'last_used_at')) {
                    $table->timestamp('last_used_at')->nullable()->after('purchased_at');
                }
            });
        }

        if (! Schema::hasTable('daily_attendances')) {
            Schema::create('daily_attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('attendance_date');
                $table->string('source')->default('claim');
                $table->unsignedInteger('reward_amount')->default(0);
                $table->foreignId('shop_item_id')->nullable()->constrained('companion_shop_items')->nullOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'attendance_date']);
            });
        }

        DB::table('companion_shop_items')
            ->whereIn('slug', ['sunset-ribbon', 'library-cape', 'study-lantern', 'storybook-badge'])
            ->update([
                'stackable' => false,
                'benefit_key' => null,
            ]);

        if (! DB::table('companion_shop_items')->where('slug', 'makeup-checkin-card')->exists()) {
            DB::table('companion_shop_items')->insert([
                'slug' => 'makeup-checkin-card',
                'name' => 'Makeup Check-In Card',
                'type' => 'consumable',
                'stackable' => true,
                'description' => 'Use one card to repair one missed sign-in day from this month.',
                'price' => 80,
                'rarity' => 'utility',
                'benefit_key' => 'makeup_checkin',
                'visual' => json_encode([
                    'accent' => '#C95F43',
                    'surface' => '#FBE4DB',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! DB::table('companion_shop_items')->where('slug', 'double-gold-ticket')->exists()) {
            DB::table('companion_shop_items')->insert([
                'slug' => 'double-gold-ticket',
                'name' => 'Double Gold Ticket',
                'type' => 'consumable',
                'stackable' => true,
                'description' => 'A utility ticket reserved for later reward multipliers. Added now as a collectible consumable.',
                'price' => 140,
                'rarity' => 'rare',
                'benefit_key' => 'double_gold_ticket',
                'visual' => json_encode([
                    'accent' => '#D4B970',
                    'surface' => '#FFF3CF',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('daily_attendances')) {
            Schema::dropIfExists('daily_attendances');
        }

        if (Schema::hasTable('companion_inventories')) {
            Schema::table('companion_inventories', function (Blueprint $table) {
                if (Schema::hasColumn('companion_inventories', 'last_used_at')) {
                    $table->dropColumn('last_used_at');
                }

                if (Schema::hasColumn('companion_inventories', 'quantity')) {
                    $table->dropColumn('quantity');
                }
            });
        }

        if (Schema::hasTable('companion_shop_items')) {
            DB::table('companion_shop_items')
                ->whereIn('slug', ['makeup-checkin-card', 'double-gold-ticket'])
                ->delete();

            Schema::table('companion_shop_items', function (Blueprint $table) {
                if (Schema::hasColumn('companion_shop_items', 'benefit_key')) {
                    $table->dropColumn('benefit_key');
                }

                if (Schema::hasColumn('companion_shop_items', 'stackable')) {
                    $table->dropColumn('stackable');
                }
            });
        }
    }
};
