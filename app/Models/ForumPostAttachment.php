<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ForumPostAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_post_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ForumPostAttachment $attachment) {
            $attachment->deleteFile();
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function url(): string
    {
        return '/storage/'.ltrim($this->path, '/');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function deleteFile(): void
    {
        if ($this->path && Storage::disk('public')->exists($this->path)) {
            Storage::disk('public')->delete($this->path);
        }
    }
}
