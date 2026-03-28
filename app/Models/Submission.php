<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id', 'exercise_id', 'article_id',
        'user_answer', 'score', 'time_spent',
        'attempt_count', 'ai_advice'
    ];
    
    protected $casts = [
        'user_answer' => 'array',
        'ai_advice' => 'array',
        'score' => 'decimal:2',
        'time_spent' => 'integer',
        'attempt_count' => 'integer',
        'created_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
    
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}