<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingQuestion extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'article_id',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
