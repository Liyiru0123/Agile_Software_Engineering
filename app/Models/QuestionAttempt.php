<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAttempt extends Model
{
    use HasFactory;

    protected $table = 'question_attempts';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'question_id',
        'user_answer',
        'is_correct',
        'is_added_wrong',
        'created_at',
        'updated_at',
    ];
}
