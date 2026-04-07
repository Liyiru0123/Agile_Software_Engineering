<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_notifications') || Schema::hasColumn('forum_notifications', 'target_forum_comment_id')) {
            return;
        }

        Schema::table('forum_notifications', function (Blueprint $table) {
            $table->foreignId('target_forum_comment_id')
                ->nullable()
                ->after('forum_comment_id')
                ->constrained('forum_comments')
                ->nullOnDelete();

            $table->index(['user_id', 'target_forum_comment_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('forum_notifications') || ! Schema::hasColumn('forum_notifications', 'target_forum_comment_id')) {
            return;
        }

        Schema::table('forum_notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_forum_comment_id');
        });
    }
};
