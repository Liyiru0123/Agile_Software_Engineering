<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['speaking', 'writing']);  // 题型
            $table->text('prompt');                         // AI 提示词
        });
    }

    public function down(): void {
        Schema::dropIfExists('ai_prompts');
    }
};