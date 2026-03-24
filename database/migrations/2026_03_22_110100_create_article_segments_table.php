<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_segments', function (Blueprint $table) {
            $table->increments('segment_id');
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('paragraph_index');
            $table->unsignedInteger('sentence_index');
            $table->text('content_en');
            $table->text('content_cn')->nullable();
            $table->decimal('start_time', 8, 3)->nullable();
            $table->decimal('end_time', 8, 3)->nullable();


            $table->index(['article_id', 'paragraph_index'], 'idx_article_segment');
            $table->foreign('article_id')->references('article_id')->on('articles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_segments');
    }
};
