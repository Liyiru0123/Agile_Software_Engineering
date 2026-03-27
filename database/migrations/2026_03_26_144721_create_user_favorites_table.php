<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // 关联用户
            $table->foreignId('article_id')->constrained()->onDelete('cascade');  // 关联文章
            $table->timestamp('created_at')->useCurrent();  // 收藏时间
            
            // 防止同一用户重复收藏同一篇文章
            $table->unique(['user_id', 'article_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_favorites');
    }
};