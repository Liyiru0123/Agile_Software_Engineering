<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_vocabulary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');                               // 关联用户
            $table->foreignId('word_id')->constrained('vocabulary')->onDelete('cascade');                   // 关联词库
            $table->foreignId('source_article_id')->nullable()->constrained('articles')->nullOnDelete();    // 来源文章（可选）
            $table->timestamp('created_at')->useCurrent();                                                  // 收藏时间
            
            // 防止同一用户重复收藏同一单词
            $table->unique(['user_id', 'word_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_vocabulary');
    }
};