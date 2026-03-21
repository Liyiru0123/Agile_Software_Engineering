<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reading_history';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'article_id',
        'progress',
        'read_at',
    ];
}
