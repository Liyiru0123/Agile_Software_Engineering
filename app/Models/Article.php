<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    use HasFactory;

    protected $primaryKey = 'article_id';
    public $incrementing = true;

    protected $fillable = [
        'title',
        'subject',
        'slug',
        'content',
        'author',
        'source',
        'level',
        'read_count',
        'excerpt',
        'word_count'
    ];

    protected $casts = [
        'read_count' => 'integer',
        'word_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Route model binding key name
    public function getRouteKeyName()
    {
        return 'article_id';
    }

    // Relationship: Has many Favorite models (one-to-many)
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'article_id', 'article_id');
    }

    // Accessor: Check if the article is favorited by current user
    public function getIsFavoritedAttribute()
    {
        if (!Auth::check()) return false;
        return $this->favorites()->where('user_id', Auth::id())->exists();
    }

    // Relationship: Has one ReadingHistory model (one-to-one for current user)
    public function readingHistory()
    {
        if (!Auth::check()) return null;
        return $this->hasOne(ReadingHistory::class, 'article_id', 'article_id')
            ->where('user_id', Auth::id())
            ->withDefault();
    }

    // Relationship: Has many Question models (one article has multiple questions)
    public function questions()
    {
        return $this->hasMany(Question::class, 'article_id', 'article_id');
    }
}