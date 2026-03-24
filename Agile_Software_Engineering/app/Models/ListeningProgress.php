<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListeningProgress extends Model
{
    public $incrementing = false;

    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'article_id',
        'last_position',
        'playback_speed',
        'is_completed',
        'listen_count',
    ];

    protected $casts = [
        'last_position' => 'integer',
        'playback_speed' => 'float',
        'is_completed' => 'boolean',
        'listen_count' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }
}
