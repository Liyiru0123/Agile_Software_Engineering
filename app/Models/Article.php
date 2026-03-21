<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'article_id';

    protected $fillable = [
        'subject',
        'title',
        'slug',
        'content',
        'author',
        'source',
        'level',
        'read_count',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'article_id', 'article_id');
    }
}
