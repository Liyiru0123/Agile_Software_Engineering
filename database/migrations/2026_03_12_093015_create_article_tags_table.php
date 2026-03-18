<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_tags', function (Blueprint $table) {
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('tag_id');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->primary(['article_id', 'tag_id']);
            $table->index('tag_id');

            $table->foreign('article_id')->references('article_id')->on('articles')->onDelete('cascade');
            $table->foreign('tag_id')->references('tag_id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_tags');
    }
};
