<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');       // 关联用户
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');   // 关联题目
            $table->foreignId('article_id')->constrained()->onDelete('cascade');    // 关联文章（冗余）
            $table->json('user_answer');                                            // 用户答案
            //$table->string('audio_path', 500)->nullable();                          // 语音文件路径（口语题用）
            $table->decimal('score', 5, 2)->nullable();                             // 本题得分
            $table->integer('time_spent')->default(0);                              // 学习时长（秒）
            $table->integer('attempt_count')->default(1);                           // 尝试次数
            $table->json('ai_advice')->nullable();                                  // AI 建议
            $table->timestamp('created_at')->useCurrent();                          // 提交时间
        });
    }

    public function down(): void {
        Schema::dropIfExists('submissions');
    }
};