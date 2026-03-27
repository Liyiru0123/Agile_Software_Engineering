<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');                            // 关联文章
            $table->enum('type', ['listening', 'reading', 'speaking', 'writing']);                          // 题型
            $table->json('question_data');                                                                  // 题目具体内容
            $table->json('answer')->nullable();                                                             // 正确答案（听力/阅读用，可为 NULL）
            $table->foreignId('ai_prompt_id')->nullable()->constrained('ai_prompts')->nullOnDelete();       // AI 配置（口语/写作用）
        });
    }

    public function down(): void {
        Schema::dropIfExists('exercises');
    }
};