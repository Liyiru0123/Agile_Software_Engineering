<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('word_translate_records', function (Blueprint $table) {
         
            $table->id();
            
     
            $table->foreignId('user_id')
                  ->constrained('users') // 关联users表
                  ->onDelete('cascade'); // 用户删除时，关联记录也删除
            

            $table->string('word', 100)->comment('待翻译的单词/短语');
            

            $table->string('source_lang', 10)->default('en')->comment('源语言（en/zh等）');
            $table->string('target_lang', 10)->default('zh')->comment('目标语言（zh/en等）');
            

            $table->text('translation')->comment('翻译结果');
            

            $table->tinyInteger('is_collect')->default(0)->comment('是否加入生词本：0-否，1-是');
            

            $table->timestamps();
            

            $table->index(['user_id', 'word']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('word_translate_records');
    }
};