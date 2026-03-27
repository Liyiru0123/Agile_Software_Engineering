<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    protected $fillable = ['word', 'phonetic', 'definition', 'audio_url'];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
}