<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPresence extends Model
{
    protected $fillable = [
        'user_id',
        'current_path',
        'is_video_available',
        'last_seen_at',
    ];

    protected $casts = [
        'is_video_available' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecentlyOnline(Builder $query, ?\DateTimeInterface $cutoff = null): Builder
    {
        $cutoff ??= now()->subMinutes(2);

        return $query->where('last_seen_at', '>=', $cutoff);
    }
}
