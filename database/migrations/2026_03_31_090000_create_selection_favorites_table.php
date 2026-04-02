<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('selection_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('paragraph_index');
            $table->string('selected_text', 191);
            $table->text('translated_text');
            $table->text('paragraph_text');
            $table->string('source_language', 16)->nullable();
            $table->string('target_language', 16)->default('zh-CN');
            $table->string('provider', 32)->default('langbly');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                ['user_id', 'article_id', 'paragraph_index', 'selected_text', 'target_language'],
                'selection_favorites_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selection_favorites');
    }
};
