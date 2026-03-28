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

    public $timestamps = false;
}