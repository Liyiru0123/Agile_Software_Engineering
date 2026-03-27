<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlan extends Model
{
    protected $fillable = [
        'user_id', 'article_id', 'plan_date',
        'status', 'completed_at'
    ];
    
    protected $casts = [
        'plan_date' => 'date',
        'completed_at' => 'datetime',
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