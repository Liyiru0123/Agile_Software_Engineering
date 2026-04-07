<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_post_attachments')) {
            Schema::create('forum_post_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('forum_post_id')->constrained('forum_posts')->cascadeOnDelete();
                $table->string('path');
                $table->string('original_name')->nullable();
                $table->string('mime_type', 120)->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('forum_comment_attachments')) {
            Schema::create('forum_comment_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('forum_comment_id')->constrained('forum_comments')->cascadeOnDelete();
                $table->string('path');
                $table->string('original_name')->nullable();
                $table->string('mime_type', 120)->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('forum_posts') && Schema::hasColumn('forum_posts', 'attachment_path')) {
            $legacyPostAttachments = DB::table('forum_posts')
                ->select('id', 'attachment_path', 'attachment_original_name', 'attachment_mime_type', 'attachment_size', 'created_at', 'updated_at')
                ->whereNotNull('attachment_path')
                ->get()
                ->map(function ($post) {
                    return [
                        'forum_post_id' => $post->id,
                        'path' => $post->attachment_path,
                        'original_name' => $post->attachment_original_name,
                        'mime_type' => $post->attachment_mime_type,
                        'size' => $post->attachment_size,
                        'sort_order' => 0,
                        'created_at' => $post->created_at ?? now(),
                        'updated_at' => $post->updated_at ?? now(),
                    ];
                })
                ->all();

            foreach ($legacyPostAttachments as $attachment) {
                $exists = DB::table('forum_post_attachments')
                    ->where('forum_post_id', $attachment['forum_post_id'])
                    ->where('path', $attachment['path'])
                    ->exists();

                if (! $exists) {
                    DB::table('forum_post_attachments')->insert($attachment);
                }
            }
        }

        if (Schema::hasTable('forum_comments') && Schema::hasColumn('forum_comments', 'attachment_path')) {
            $legacyCommentAttachments = DB::table('forum_comments')
                ->select('id', 'attachment_path', 'attachment_original_name', 'attachment_mime_type', 'attachment_size', 'created_at', 'updated_at')
                ->whereNotNull('attachment_path')
                ->get()
                ->map(function ($comment) {
                    return [
                        'forum_comment_id' => $comment->id,
                        'path' => $comment->attachment_path,
                        'original_name' => $comment->attachment_original_name,
                        'mime_type' => $comment->attachment_mime_type,
                        'size' => $comment->attachment_size,
                        'sort_order' => 0,
                        'created_at' => $comment->created_at ?? now(),
                        'updated_at' => $comment->updated_at ?? now(),
                    ];
                })
                ->all();

            foreach ($legacyCommentAttachments as $attachment) {
                $exists = DB::table('forum_comment_attachments')
                    ->where('forum_comment_id', $attachment['forum_comment_id'])
                    ->where('path', $attachment['path'])
                    ->exists();

                if (! $exists) {
                    DB::table('forum_comment_attachments')->insert($attachment);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_comment_attachments');
        Schema::dropIfExists('forum_post_attachments');
    }
};
