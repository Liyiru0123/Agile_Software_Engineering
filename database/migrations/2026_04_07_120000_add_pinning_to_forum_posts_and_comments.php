<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('view_count');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
        });

        Schema::table('forum_comments', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('reply_to_comment_id');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('forum_comments', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'pinned_at']);
        });

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'pinned_at']);
        });
    }
};
