<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_tags') || ! Schema::hasTable('forum_posts') || ! Schema::hasTable('users')) {
            return;
        }

        $ownerId = DB::table('users')
            ->where('is_admin', true)
            ->orderBy('id')
            ->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        if (! $ownerId) {
            return;
        }

        $publicTag = DB::table('forum_tags')
            ->where('slug', 'public-forum')
            ->orWhere('name', 'Public Forum')
            ->first();

        if (! $publicTag) {
            $tagId = DB::table('forum_tags')->insertGetId([
                'user_id' => $ownerId,
                'name' => 'Public Forum',
                'slug' => 'public-forum',
                'description' => 'Open discussion for general learning reflections, questions, and study updates.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $tagId = $publicTag->id;

            DB::table('forum_tags')
                ->where('id', $tagId)
                ->update([
                    'user_id' => $publicTag->user_id ?: $ownerId,
                    'name' => 'Public Forum',
                    'slug' => 'public-forum',
                    'description' => $publicTag->description ?: 'Open discussion for general learning reflections, questions, and study updates.',
                    'updated_at' => now(),
                ]);
        }

        DB::table('forum_posts')
            ->whereNull('forum_tag_id')
            ->update(['forum_tag_id' => $tagId]);
    }

    public function down(): void
    {
        //
    }
};
