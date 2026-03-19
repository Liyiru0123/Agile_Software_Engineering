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
        if (Schema::hasTable('wrong_questions')) {
            return;
        }

        Schema::create('wrong_questions', function (Blueprint $table) {
            $table->increments('wrong_question_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('question_id');
            $table->text('user_answer')->nullable()->comment('用户答题时的答案(JSON格式)');
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('question_id');

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wrong_questions');
    }
};
