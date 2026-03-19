<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Favorite;
use App\Models\ReadingHistory;
use App\Models\QuestionAttempt;
use App\Models\WrongQuestion;

class User extends Authenticatable
{
    use Notifiable;

    // Primary key configuration
    protected $primaryKey = 'user_id';

    // Mass assignable attributes
    protected $fillable = [
        'name', 'email', 'password', 'is_admin'
    ];

    // Hidden attributes for serialization
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Attribute type casting
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean'
        ];
    }

    // Relationship: User has many Favorites
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'user_id', 'user_id');
    }

    // Relationship: User has many ReadingHistories
    public function readingHistories(): HasMany
    {
        return $this->hasMany(ReadingHistory::class, 'user_id', 'user_id');
    }

    // Relationship: User has many QuestionAttempts
    public function questionAttempts(): HasMany
    {
        return $this->hasMany(QuestionAttempt::class, 'user_id', 'user_id');
    }

    // Relationship: User has many WrongQuestions
    public function wrongQuestions(): HasMany
    {
        return $this->hasMany(WrongQuestion::class, 'user_id', 'user_id');
    }
}