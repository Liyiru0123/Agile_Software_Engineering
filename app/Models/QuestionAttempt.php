<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAttempt extends Model
{
    use HasFactory;

    protected $primaryKey = 'attempt_id';
    public $incrementing = true;

    protected $fillable = [
        'user_id',       // User ID of the person who answered the question
        'question_id',   // Question ID
        'user_answer',   // User's answer (JSON format)
        'is_correct'     // Whether the answer is correct (0/1)
    ];

    protected $casts = [
        'user_answer' => 'array',  // Auto convert JSON to array
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship: Belongs to User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship: Belongs to Question model
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }
}