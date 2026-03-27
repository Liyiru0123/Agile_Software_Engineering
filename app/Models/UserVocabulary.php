<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVocabulary extends Model
{
    protected $fillable = ['user_id', 'word_id', 'source_article_id'];
    
    public $timestamps = false;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function word(): BelongsTo
    {
        return $this->belongsTo(Vocabulary::class, 'word_id');
    }
    
    public function sourceArticle(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'source_article_id');
    }
}