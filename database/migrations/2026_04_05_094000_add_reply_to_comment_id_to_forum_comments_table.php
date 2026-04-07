<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forum_comments') && ! Schema::hasColumn('forum_comments', 'reply_to_comment_id')) {
            Schema::table('forum_comments', function (Blueprint $table) {
                $table->foreignId('reply_to_comment_id')
                    ->nullable()
                    ->after('forum_post_id')
                    ->constrained('forum_comments')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('forum_comments') && Schema::hasColumn('forum_comments', 'reply_to_comment_id')) {
            Schema::table('forum_comments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('reply_to_comment_id');
            });
        }
    }
};
