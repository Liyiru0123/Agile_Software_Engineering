<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('article_segments')) {
            return;
        }

        Schema::create('article_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('paragraph_index');
            $table->unsignedInteger('sentence_index')->default(0);
            $table->longText('content_en');
            $table->longText('content_cn')->nullable();
            $table->decimal('start_time', 8, 2)->unsigned()->nullable();
            $table->decimal('end_time', 8, 2)->unsigned()->nullable();
            $table->timestamps();

            $table->index(['article_id', 'paragraph_index', 'sentence_index'], 'article_segments_article_paragraph_sentence_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_segments');
    }
};
