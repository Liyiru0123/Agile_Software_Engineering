<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    // Primary key configuration
    protected $primaryKey = 'question_id';
    public $incrementing = true;

    // Mass assignable attributes
    protected $fillable = [
        'article_id',   // Associated article ID
        'content',      // Question content
        'options',      // Options (JSON string, auto converted to array)
        'answer',       // Correct answer (stored as JSON string, e.g. ["A","C"], auto converted to array)
        'type',         // Question type: single=single choice, multiple=multiple choice
        'explanation'   // Answer explanation
    ];

    // Attribute type casting (auto convert JSON to array)
    protected $casts = [
        'options' => 'array',          // JSON string → array
        'answer' => 'array',           // JSON string → array
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Route model binding key name
    public function getRouteKeyName()
    {
        return 'question_id';
    }

    // Accessor: Ensure options always return array (fallback for empty/invalid JSON)
    public function getOptionsAttribute($value)
    {
        $array = json_decode($value, true);
        return is_array($array) ? $array : [];
    }

    // Accessor: Ensure answer always return array (fallback for empty/invalid JSON)
    public function getAnswerAttribute($value)
    {
        $array = json_decode($value, true);
        return is_array($array) ? $array : [];
    }

    // Relationship: Belongs to Article model
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }

    // Relationship: Has many QuestionAttempt models (one question has multiple attempt records)
    public function questionAttempts()
    {
        return $this->hasMany(QuestionAttempt::class, 'question_id', 'question_id');
    }

    // Relationship: Has many WrongQuestion models (one question has multiple wrong records)
    public function wrongQuestions()
    {
        return $this->hasMany(WrongQuestion::class, 'question_id', 'question_id');
    }
}