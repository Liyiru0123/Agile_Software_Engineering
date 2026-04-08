<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleSegment extends Model
{
    protected $fillable = [
        'article_id',
        'paragraph_index',
        'sentence_index',
        'content_en',
        'content_cn',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'article_id' => 'integer',
        'paragraph_index' => 'integer',
        'sentence_index' => 'integer',
        'start_time' => 'float',
        'end_time' => 'float',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
