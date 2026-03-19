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
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('question_id');
            $table->unsignedInteger('article_id');
            $table->text('content');
            $table->json('options')->comment('JSON format: {"A":"option content","B":"option content"...}');
            $table->string('answer', 10);
            $table->enum('type', ['single', 'multiple'])->default('single')->comment('题目类型：single=单选，multiple=多选');
            $table->text('explanation')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('article_id');
            $table->foreign('article_id')->references('article_id')->on('articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
