<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'article_id',
        'plan_date',
        'plan_kind',
        'title',
        'skill_type',
        'target_count',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'plan_date' => 'date',
        'target_count' => 'integer',
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

    public function displayTitle(): string
    {
        if ($this->plan_kind === 'skill' && $this->skill_type && $this->target_count) {
            return ucfirst($this->skill_type).' practice x'.$this->target_count;
        }

        if ($this->title) {
            return $this->title;
        }

        return $this->article?->title ?? 'Untitled Plan';
    }
}
