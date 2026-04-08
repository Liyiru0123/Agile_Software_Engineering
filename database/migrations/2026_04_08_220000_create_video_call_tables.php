<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_presences')) {
            Schema::create('user_presences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('current_path', 255)->nullable();
                $table->boolean('is_video_available')->default(false);
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();

                $table->unique('user_id');
                $table->index(['is_video_available', 'last_seen_at']);
            });
        }

        if (! Schema::hasTable('video_call_queues')) {
            Schema::create('video_call_queues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('status', 20)->default('searching');
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('matched_at')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();

                $table->unique('user_id');
                $table->index(['status', 'last_seen_at']);
            });
        }

        if (! Schema::hasTable('video_call_sessions')) {
            Schema::create('video_call_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('mode', 20)->default('friend');
                $table->string('status', 20)->default('ringing');
                $table->foreignId('host_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('guest_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->string('daily_room_name', 160)->nullable()->unique();
                $table->string('daily_room_url', 255)->nullable();
                $table->json('daily_payload')->nullable();
                $table->timestamp('room_expires_at')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamp('declined_at')->nullable();
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'created_at']);
                $table->index(['host_user_id', 'status']);
                $table->index(['guest_user_id', 'status']);
                $table->index(['mode', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('video_call_sessions');
        Schema::dropIfExists('video_call_queues');
        Schema::dropIfExists('user_presences');
    }
};
