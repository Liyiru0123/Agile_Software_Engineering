<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ForumComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'forum_post_id',
        'reply_to_comment_id',
        'body',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime_type',
        'attachment_size',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ForumComment $comment) {
            $comment->attachments()->get()->each(function (ForumCommentAttachment $attachment) {
                $attachment->delete();
            });
            $comment->deleteAttachmentFile();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function replyParent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_comment_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'reply_to_comment_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ForumCommentAttachment::class)
            ->orderBy('sort_order')
            ->orderBy('id');
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
