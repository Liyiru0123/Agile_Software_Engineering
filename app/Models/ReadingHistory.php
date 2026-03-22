<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingHistory extends Model
{
    protected $table = 'reading_history';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'article_id',
        'is_completed',
        'read_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }
}
