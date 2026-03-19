<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sessions')) {
            return;
        }

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Session ID（对应报错里的 WVH1Z8QE8N61...）
            $table->foreignId('user_id')->nullable()->index(); // 关联用户ID（预留）
            $table->string('ip_address', 45)->nullable(); // 客户端IP
            $table->text('user_agent')->nullable(); // 浏览器信息
            $table->longText('payload'); // Session 数据
            $table->integer('last_activity')->index(); // 最后活动时间戳
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};