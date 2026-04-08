<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ForumCommentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_comment_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ForumCommentAttachment $attachment) {
            $attachment->deleteFile();
        });
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(ForumComment::class, 'forum_comment_id');
    }

    public function url(): string
    {
        if ($this->isExternalPath()) {
            return $this->path;
        }

        return '/storage/'.ltrim($this->path, '/');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function deleteFile(): void
    {
        if ($this->isExternalPath()) {
            return;
        }

        if ($this->path && Storage::disk('public')->exists($this->path)) {
            Storage::disk('public')->delete($this->path);
        }
    }

    protected function isExternalPath(): bool
    {
        return str_starts_with($this->path, 'http://')
            || str_starts_with($this->path, 'https://')
            || str_starts_with($this->path, '//');
    }
}
