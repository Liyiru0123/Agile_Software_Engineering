<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoCallQueue extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'requested_at',
        'matched_at',
        'last_seen_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'matched_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
