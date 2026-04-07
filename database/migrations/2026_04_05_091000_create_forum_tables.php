<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_tags')) {
            Schema::create('forum_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('name', 80)->unique();
                $table->string('slug', 100)->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('forum_posts')) {
            Schema::create('forum_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('forum_tag_id')->nullable()->constrained('forum_tags')->nullOnDelete();
                $table->string('title', 160);
                $table->text('body');
                $table->unsignedInteger('view_count')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('forum_comments')) {
            Schema::create('forum_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('forum_post_id')->constrained('forum_posts')->cascadeOnDelete();
                $table->text('body');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_tags');
    }
};
