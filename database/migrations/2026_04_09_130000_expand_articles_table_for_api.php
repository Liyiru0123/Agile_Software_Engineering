<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'subject')) {
                $table->string('subject', 120)->nullable();
            }

            if (! Schema::hasColumn('articles', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }

            if (! Schema::hasColumn('articles', 'author')) {
                $table->string('author', 100)->nullable();
            }

            if (! Schema::hasColumn('articles', 'source')) {
                $table->string('source', 255)->nullable();
            }

            if (! Schema::hasColumn('articles', 'level')) {
                $table->string('level', 40)->nullable();
            }

            if (! Schema::hasColumn('articles', 'resource_type')) {
                $table->string('resource_type', 20)->default('text');
            }

            if (! Schema::hasColumn('articles', 'accent')) {
                $table->string('accent', 10)->default('US');
            }

            if (! Schema::hasColumn('articles', 'video_url')) {
                $table->string('video_url', 500)->nullable();
            }

            if (! Schema::hasColumn('articles', 'total_duration')) {
                $table->integer('total_duration')->default(0);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            foreach (['subject', 'author', 'source', 'level', 'resource_type', 'accent', 'video_url', 'total_duration'] as $column) {
                if (Schema::hasColumn('articles', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('articles', 'slug')) {
                try {
                    $table->dropUnique('articles_slug_unique');
                } catch (Throwable $exception) {
                }

                $table->dropColumn('slug');
            }
        });
    }
};