<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'forum_tag_id',
        'title',
        'body',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime_type',
        'attachment_size',
        'view_count',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ForumPost $post) {
            $post->attachments()->get()->each(function (ForumPostAttachment $attachment) {
                $attachment->delete();
            });

            $post->deleteAttachmentFile();

            $post->comments()->get()->each(function (ForumComment $comment) {
                $comment->attachments()->get()->each(function (ForumCommentAttachment $attachment) {
                    $attachment->delete();
                });
                $comment->deleteAttachmentFile();
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(ForumTag::class, 'forum_tag_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ForumPostAttachment::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_post_likes')
            ->withTimestamps();
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_post_favorites')
            ->withTimestamps();
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path);
    }

    public function hasLegacyAttachment(): bool
    {
        return filled($this->attachment_path);
    }

    public function hasAnyAttachments(): bool
    {
        if ($this->relationLoaded('attachments')) {
            return $this->attachments->isNotEmpty() || $this->hasLegacyAttachment();
        }

        return $this->hasLegacyAttachment() || $this->attachments()->exists();
    }

    public function isImageAttachment(): bool
    {
        return $this->hasAttachment() && str_starts_with((string) $this->attachment_mime_type, 'image/');
    }

    public function attachmentUrl(): ?string
    {
        return $this->hasAttachment() ? '/storage/'.ltrim($this->attachment_path, '/') : null;
    }

    public function deleteAttachmentFile(): void
    {
        if ($this->attachment_path && Storage::disk('public')->exists($this->attachment_path)) {
            Storage::disk('public')->delete($this->attachment_path);
        }
    }
}
