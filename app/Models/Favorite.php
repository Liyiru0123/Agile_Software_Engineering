<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 1. Remove SoftDeletes import
// use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon; // Import Carbon for date/time handling

class Favorite extends Model
{
    // 2. Remove SoftDeletes Trait
    use HasFactory;
    // use HasFactory, SoftDeletes;

    protected $primaryKey = ['user_id', 'article_id'];
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'article_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 3. Remove deleted_at field configuration
        // 'deleted_at' => 'datetime',
    ];

    // Critical Fix 3: Compatibility for composite primary key query/save
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->primaryKey as $key) {
            $query->where($key, $this->{$key});
        }
        return $query;
    }

    // Optional: Accessor for created_at (fallback solution) if table has no timestamps
    public function getCreatedAtAttribute($value)
    {
        // Compatibility for string/NULL values, convert to Carbon object
        return $value ? Carbon::parse($value) : Carbon::now();
    }

    // Relationship: Belongs to User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship: Belongs to Article model
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }
}