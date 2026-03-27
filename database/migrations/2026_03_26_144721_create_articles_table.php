<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();                                       // 主键
            $table->string('title', 200);                       // 标题
            $table->text('content');                            // 纯文本内容
            $table->string('audio_url', 500)->nullable();       // 音频链接（可选）
            $table->tinyInteger('difficulty')->default(1);      // 难度 1-3
            $table->integer('word_count')->default(0);          // 单词数
        });
    }

    public function down(): void {
        Schema::dropIfExists('articles');
    }
};