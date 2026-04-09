<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'subject',
        'title',
        'slug',
        'author',
        'source',
        'level',
        'content',
        'audio_url',
        'video_url',
        'resource_type',
        'accent',
        'difficulty',
        'word_count',
        'total_duration',
    ];

    protected $casts = [
        'difficulty' => 'integer',
        'word_count' => 'integer',
        'total_duration' => 'integer',
    ];

    protected $appends = ['has_audio'];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            $article->content ??= '';
        });
    }

    public function getHasAudioAttribute(): bool
    {
        return filled($this->audio_url);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function readingQuestions(): HasMany
    {
        return $this->hasMany(ReadingQuestion::class);
    }

    public function segments(): HasMany
    {
        return $this->hasMany(ArticleSegment::class);
    }
}