<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $primaryKey = 'article_id';

    protected $fillable = [
        'subject',
        'title',
        'slug',
        'audio_url',
        'video_url',
        'author',
        'source',
        'level',
        'accent',
        'total_duration',
        'resource_type',
        'word_count',
    ];

    protected $appends = [
        'id',
        'content',
        'has_audio',
    ];

    protected $casts = [
        'word_count' => 'integer',
        'total_duration' => 'integer',
    ];

    public function segments(): HasMany
    {
        return $this->hasMany(ArticleSegment::class, 'article_id', 'article_id')
            ->orderBy('paragraph_index')
            ->orderBy('sentence_index');
    }

    public function getIdAttribute(): int
    {
        return (int) $this->getKey();
    }

    public function getAudioUrlAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/storage/'])) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    public function getContentAttribute(): string
    {
        $segments = $this->relationLoaded('segments')
            ? $this->segments
            : $this->segments()->get(['paragraph_index', 'sentence_index', 'content_en']);

        return $segments
            ->groupBy('paragraph_index')
            ->map(fn ($group) => $group->pluck('content_en')->implode(' '))
            ->implode("\n\n");
    }

    public function getHasAudioAttribute(): bool
    {
        return filled($this->getRawOriginal('audio_url'));
    }
}
