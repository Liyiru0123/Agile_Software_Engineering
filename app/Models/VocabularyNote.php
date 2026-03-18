<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VocabularyNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'vocabulary_note_id';

    protected $fillable = [
        'user_id',
        'word',
        'definition',
        'example',
    ];
}
