<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('article_id');
            $table->enum('subject', [
                'Civil Engineering',
                'Mathematics',
                'Computer Science',
                'Mechanical Engineering',
                'Mechanical Engineering with Transportation',
            ]);
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('audio_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('author', 100)->nullable();
            $table->string('source')->nullable();
            $table->enum('level', ['Easy', 'Intermediate', 'Advanced']);
            $table->enum('accent', ['US', 'UK'])->nullable()->default('US');
            $table->integer('total_duration')->nullable()->default(0);
            $table->enum('resource_type', ['text', 'audio', 'video'])->nullable()->default('text');
            $table->integer('word_count')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
