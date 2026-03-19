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
            if (!Schema::hasColumn('articles', 'excerpt')) {
                $table->text('excerpt')->nullable()->comment('文章摘要');
            }

            if (!Schema::hasColumn('articles', 'word_count')) {
                $table->integer('word_count')->default(0)->comment('单词数');
            }

            if (!Schema::hasColumn('articles', 'views')) {
                $table->integer('views')->default(0)->comment('阅读量');
            }

            if (!Schema::hasColumn('articles', 'category')) {
                $table->string('category', 20)->nullable()->comment('分类（science/life/culture）');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['excerpt', 'word_count', 'views', 'category'] as $column) {
                if (Schema::hasColumn('articles', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
