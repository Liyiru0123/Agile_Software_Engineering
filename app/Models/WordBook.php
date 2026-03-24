<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WordBook extends Model
{
    use HasFactory;

    protected $table = 'word_books';


    protected $fillable = [
        'user_id',
        'word',
        'phonetic',
        'paraphrase',
        'mem_status'
    ];

    protected $hidden = [];


    protected $casts = [
        'mem_status' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}