<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelectionFavorite extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'article_id',
        'paragraph_index',
        'selected_text',
        'translated_text',
        'paragraph_text',
        'source_language',
        'target_language',
        'provider',
    ];

    protected $casts = [
        'paragraph_index' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
