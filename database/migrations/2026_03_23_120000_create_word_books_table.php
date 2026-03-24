<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('word_books', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('User ID'); // Associate with users table
            $table->string('word', 100)->comment('Word');
            $table->string('phonetic', 200)->nullable()->comment('Phonetic symbol');
            $table->text('paraphrase')->nullable()->comment('Paraphrase/Explanation');
            $table->tinyInteger('mem_status')->default(0)->comment('Memory status: 0-Ungrasped 1-Reviewing 2-Mastered');
            $table->timestamps(); // Automatically generate create_at / update_at

            // Unique index (Prevent users from adding the same word repeatedly)
            $table->unique(['user_id', 'word']);
            // General index (Optimize query performance)
            $table->index(['user_id', 'mem_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('word_books');
    }
};