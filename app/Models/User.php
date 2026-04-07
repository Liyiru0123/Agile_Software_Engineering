<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'user_favorites')
                    ->withPivot('created_at');
    }

    public function companionProfile(): HasOne
    {
        return $this->hasOne(CompanionProfile::class);
    }

    public function companionInventory(): HasMany
    {
        return $this->hasMany(CompanionInventory::class);
    }

    public function companionTransactions(): HasMany
    {
        return $this->hasMany(CompanionTransaction::class);
    }

    public function forumTags(): HasMany
    {
        return $this->hasMany(ForumTag::class);
    }

    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumComments(): HasMany
    {
        return $this->hasMany(ForumComment::class);
    }

    public function forumNotifications(): HasMany
    {
        return $this->hasMany(ForumNotification::class);
    }

    public function likedForumPosts(): BelongsToMany
    {
        return $this->belongsToMany(ForumPost::class, 'forum_post_likes')
            ->withTimestamps();
    }

    public function favoritedForumPosts(): BelongsToMany
    {
        return $this->belongsToMany(ForumPost::class, 'forum_post_favorites')
            ->withTimestamps();
    }

    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function friendshipsAsFirst(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_one_id');
    }

    public function friendshipsAsSecond(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_two_id');
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function sentConversationMessages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class, 'sender_id');
    }

    public function isFriendsWith(User|int $user): bool
    {
        $otherUserId = $user instanceof self ? $user->id : $user;

        if ($otherUserId === $this->id) {
            return false;
        }

        [$userOneId, $userTwoId] = Friendship::orderedPair($this->id, $otherUserId);

        return Friendship::query()
            ->where('user_one_id', $userOneId)
            ->where('user_two_id', $userTwoId)
            ->exists();
    }

    public function friendIds()
    {
        $asFirst = $this->friendshipsAsFirst()->pluck('user_two_id');
        $asSecond = $this->friendshipsAsSecond()->pluck('user_one_id');

        return $asFirst->merge($asSecond)->unique()->values();
    }

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
}
