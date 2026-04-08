<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forum_posts') && ! Schema::hasColumn('forum_posts', 'source_name')) {
            Schema::table('forum_posts', function (Blueprint $table) {
                $table->string('source_name', 120)->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('forum_posts') && Schema::hasColumn('forum_posts', 'source_name')) {
            Schema::table('forum_posts', function (Blueprint $table) {
                $table->dropColumn('source_name');
            });
        }
    }
};
