<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyNote extends Model
{
    protected $primaryKey = 'vocabulary_note_id';

    protected $fillable = [
        'user_id',
        'word',
        'definition',
        'example',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getIdAttribute(): int
    {
        return (int) $this->getKey();
    }
}
