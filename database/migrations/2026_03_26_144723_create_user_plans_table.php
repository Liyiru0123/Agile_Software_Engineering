<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');                   // 关联用户
            $table->foreignId('article_id')->constrained()->onDelete('cascade');                // 关联文章
            $table->date('plan_date');                                                          // 计划日期
            $table->enum('status', ['pending', 'completed', 'skipped'])->default('pending');    // 状态
            $table->timestamp('completed_at')->nullable();                                      // 完成时间
            
            // 索引优化：查"某用户某天的计划"
            $table->index(['user_id', 'plan_date', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_plans');
    }
};