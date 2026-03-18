<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WrongQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'wrong_question_id';
    public $incrementing = true;

    protected $fillable = [
        'user_id',       // User ID
        'question_id',   // Question ID
        'user_answer'    // User's answer at the time (JSON format)
    ];

    protected $casts = [
        'user_answer' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relate to User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relate to Question model (join to get article/question content/explanation)
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id')->with('article');
    }
}