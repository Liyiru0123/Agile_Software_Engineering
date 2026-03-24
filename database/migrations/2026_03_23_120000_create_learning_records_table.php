<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('User ID'); // Associate with users table
            $table->datetime('start_time')->comment('Learning start time'); // Precise to seconds
            $table->datetime('end_time')->nullable()->comment('Learning end time'); // Null if not completed
            $table->integer('duration')->default(0)->comment('Single learning duration (seconds), calculated after completion');
            $table->string('learning_type')->nullable()->comment('Learning type: e.g. vocabulary/article/listening (extension field)');
            $table->timestamps(); // Automatically maintain create/update time

            // Index optimization
            $table->index('user_id');
            $table->index(['user_id', 'start_time']); // Query by user + time
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_records');
    }
};