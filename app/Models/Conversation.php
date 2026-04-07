<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'type',
        'direct_key',
        'created_by',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class)->orderBy('created_at');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ConversationMessage::class)->latestOfMany();
    }

    public function hasParticipant(int $userId): bool
    {
        if ($this->relationLoaded('participants')) {
            return $this->participants->contains('id', $userId);
        }

        return $this->participants()->where('users.id', $userId)->exists();
    }

    public function otherParticipant(int $userId): ?User
    {
        if ($this->relationLoaded('participants')) {
            return $this->participants->firstWhere('id', '!=', $userId);
        }

        return $this->participants()->where('users.id', '!=', $userId)->first();
    }

    public static function directKeyFor(int $firstUserId, int $secondUserId): string
    {
        [$userOneId, $userTwoId] = Friendship::orderedPair($firstUserId, $secondUserId);

        return $userOneId.':'.$userTwoId;
    }

    public static function firstOrCreateDirectBetween(User $firstUser, User $secondUser): self
    {
        $directKey = self::directKeyFor($firstUser->id, $secondUser->id);

        $conversation = self::query()->firstOrCreate(
            ['direct_key' => $directKey],
            [
                'type' => 'direct',
                'created_by' => $firstUser->id,
            ]
        );

        $conversation->participants()->syncWithoutDetaching([
            $firstUser->id => ['last_read_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            $secondUser->id => ['last_read_at' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        return $conversation;
    }
}
