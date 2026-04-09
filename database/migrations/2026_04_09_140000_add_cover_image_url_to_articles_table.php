<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('articles') || Schema::hasColumn('articles', 'cover_image_url')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->string('cover_image_url', 500)->nullable()->after('audio_url');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('articles') || ! Schema::hasColumn('articles', 'cover_image_url')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('cover_image_url');
        });
    }
};