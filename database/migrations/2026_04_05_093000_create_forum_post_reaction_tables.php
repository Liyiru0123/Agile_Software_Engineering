<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_post_likes')) {
            Schema::create('forum_post_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('forum_post_id')->constrained('forum_posts')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'forum_post_id']);
            });
        }

        if (! Schema::hasTable('forum_post_favorites')) {
            Schema::create('forum_post_favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('forum_post_id')->constrained('forum_posts')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'forum_post_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_favorites');
        Schema::dropIfExists('forum_post_likes');
    }
};
