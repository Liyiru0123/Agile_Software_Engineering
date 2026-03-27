<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFavorite extends Model
{
    protected $fillable = ['user_id', 'article_id'];
    
    public $timestamps = false;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}