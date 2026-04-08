<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoCallSession extends Model
{
    protected $fillable = [
        'mode',
        'status',
        'host_user_id',
        'guest_user_id',
        'created_by',
        'daily_room_name',
        'daily_room_url',
        'daily_payload',
        'room_expires_at',
        'accepted_at',
        'started_at',
        'ended_at',
        'declined_at',
        'last_activity_at',
    ];

    protected $casts = [
        'daily_payload' => 'array',
        'room_expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'declined_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function involvesUser(int $userId): bool
    {
        return (int) $this->host_user_id === $userId || (int) $this->guest_user_id === $userId;
    }

    public function otherParticipantId(int $userId): ?int
    {
        if ((int) $this->host_user_id === $userId) {
            return (int) $this->guest_user_id;
        }

        if ((int) $this->guest_user_id === $userId) {
            return (int) $this->host_user_id;
        }

        return null;
    }
}
