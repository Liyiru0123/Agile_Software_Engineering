<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = ['title', 'content', 'audio_url', 'difficulty', 'word_count'];
    
    protected $casts = [
        'difficulty' => 'integer',
        'word_count' => 'integer',
    ];
    
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
    
    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    // app/Models/Article.php （补充关系方法）
    //这里也改名为readingQuestions（），ai给的是questions
    public function readingQuestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ReadingQuestion::class);
    }

    public $timestamps = false;
}