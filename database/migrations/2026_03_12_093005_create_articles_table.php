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
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('article_id');
            $table->enum('subject', [
                'Civil Engineering',
                'Mathematics',
                'Computer Science',
                'Mechanical Engineering',
                'Mechanical Engineering with Transportation',
            ]);
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('content');
            $table->string('author', 100)->nullable();
            $table->string('source', 255)->nullable();
            $table->enum('level', ['Easy', 'Intermediate', 'Advanced']);
            $table->integer('read_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
