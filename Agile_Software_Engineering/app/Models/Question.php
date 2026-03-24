<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'article_id',
        'content',
        'options',
        'answer',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }

    public function getIdAttribute(): int
    {
        return (int) $this->getKey();
    }
}
