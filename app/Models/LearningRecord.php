<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningRecord extends Model
{
    use HasFactory;


    protected $table = 'learning_records';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'duration',
        'learning_type'
    ];


    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}