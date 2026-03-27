<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vocabulary', function (Blueprint $table) {
            $table->id();
            $table->string('word', 50)->unique();           // 单词唯一
            $table->string('phonetic', 50)->nullable();     // 音标（可选）
            $table->text('definition');                     // 释义
            $table->string('audio_url', 200)->nullable();   // 音频链接（可选）
            $table->timestamp('created_at')->useCurrent();  // 创建时间
        });
    }

    public function down(): void {
        Schema::dropIfExists('vocabulary');
    }
};