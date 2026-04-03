<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingHistory extends Model
{
    protected $table = 'reading_history';

    protected $fillable = [
        'user_id',
        'article_id',
        'last_page',
        'is_completed',
        'visit_count',
        'first_viewed_at',
        'last_viewed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'visit_count' => 'integer',
        'first_viewed_at' => 'datetime',
        'last_viewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function getPageLabelAttribute(): string
    {
        return match ($this->last_page) {
            'listening' => 'Listening',
            'speaking' => 'Speaking',
            'reading' => 'Reading',
            'writing' => 'Writing',
            default => 'Article',
        };
    }

    public function getContinueUrlAttribute(): ?string
    {
        if (! $this->article_id) {
            return null;
        }

        return match ($this->last_page) {
            'listening' => route('articles.listening', $this->article_id),
            'speaking' => route('articles.speaking', $this->article_id),
            'reading' => route('articles.reading', $this->article_id),
            'writing' => route('articles.writing', $this->article_id),
            default => route('articles.show', $this->article_id),
        };
    }
}
