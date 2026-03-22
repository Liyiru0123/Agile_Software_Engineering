<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('tag_id');
            $table->string('name', 50);
            $table->string('slug', 60)->unique();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('article_tags', function (Blueprint $table) {
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('tag_id');
            $table->timestamp('created_at')->nullable();

            $table->primary(['article_id', 'tag_id']);
            $table->unique(['article_id', 'tag_id'], 'unique_article_tag');
            $table->foreign('article_id')->references('article_id')->on('articles')->cascadeOnDelete();
            $table->foreign('tag_id')->references('tag_id')->on('tags')->cascadeOnDelete();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('article_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->primary(['user_id', 'article_id']);
            $table->unique(['user_id', 'article_id'], 'unique_user_favorite');
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('article_id')->references('article_id')->on('articles')->cascadeOnDelete();
        });

        Schema::create('learning_statistics', function (Blueprint $table) {
            $table->increments('stat_id');
            $table->unsignedInteger('user_id');
            $table->date('stat_date');
            $table->integer('listening_minutes')->default(0)->nullable();
            $table->integer('speaking_minutes')->default(0)->nullable();
            $table->integer('reading_minutes')->default(0)->nullable();

            $table->unique(['user_id', 'stat_date'], 'unique_user_date');
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });

        Schema::create('listening_progress', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('article_id');
            $table->integer('last_position')->default(0)->nullable();
            $table->float('playback_speed')->default(1)->nullable();
            $table->boolean('is_completed')->default(false)->nullable();
            $table->integer('listen_count')->default(0)->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary(['user_id', 'article_id']);
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('article_id')->references('article_id')->on('articles')->cascadeOnDelete();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->increments('question_id');
            $table->unsignedInteger('article_id');
            $table->text('content');
            $table->json('options');
            $table->string('answer', 10);
            $table->text('explanation')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('article_id')->references('article_id')->on('articles')->cascadeOnDelete();
        });

        Schema::create('question_attempts', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('question_id');
            $table->string('user_answer', 10);
            $table->boolean('is_correct');
            $table->timestamp('created_at')->nullable();

            $table->primary(['user_id', 'question_id']);
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('question_id')->references('question_id')->on('questions')->cascadeOnDelete();
        });

        Schema::create('reading_history', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('article_id');
            $table->boolean('is_completed')->default(true)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->primary(['user_id', 'article_id']);
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('article_id')->references('article_id')->on('articles')->cascadeOnDelete();
        });

        Schema::create('speaking_attempts', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('segment_id');
            $table->string('record_url');
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('accuracy_score', 5, 2)->nullable();
            $table->decimal('fluency_score', 5, 2)->nullable();
            $table->json('ai_feedback_json')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->primary(['user_id', 'segment_id']);
            $table->index(['user_id', 'segment_id'], 'idx_user_segment');
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('segment_id')->references('segment_id')->on('article_segments')->cascadeOnDelete();
        });

        Schema::create('vocabulary_notes', function (Blueprint $table) {
            $table->increments('vocabulary_note_id');
            $table->unsignedInteger('user_id');
            $table->string('word', 100);
            $table->text('definition');
            $table->text('example')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_notes');
        Schema::dropIfExists('speaking_attempts');
        Schema::dropIfExists('reading_history');
        Schema::dropIfExists('question_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('listening_progress');
        Schema::dropIfExists('learning_statistics');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('article_tags');
        Schema::dropIfExists('tags');
    }
};
