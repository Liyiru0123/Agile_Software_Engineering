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
        Schema::create('vocabulary_notes', function (Blueprint $table) {
            $table->increments('vocabulary_note_id');
            $table->unsignedInteger('user_id');
            $table->string('word', 100);
            $table->text('definition');
            $table->text('example')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id', 'fk_vocabulary_notes_users1_idx');
            $table->foreign('user_id', 'fk_vocabulary_notes_users1')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_notes');
    }
};
