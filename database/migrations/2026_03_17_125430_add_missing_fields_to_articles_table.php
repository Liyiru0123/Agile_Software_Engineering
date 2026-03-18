<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('excerpt')->comment('文章摘要');
            $table->integer('word_count')->default(0)->comment('单词数');
            $table->string('author', 100)->default('管理员')->comment('作者');
            $table->integer('views')->default(0)->comment('阅读量');
            $table->string('category', 20)->comment('分类（science/life/culture）');
            $table->string('level', 20)->comment('难度（easy/intermediate/advanced）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'word_count', 'author', 'views', 'category', 'level']);
        });
    }
};
