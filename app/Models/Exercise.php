<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    protected $fillable = ['article_id', 'type', 'question_data', 'answer', 'ai_prompt_id'];
    
    protected $casts = [
        'question_data' => 'array',
        'answer' => 'array',
    ];
    
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
    
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}