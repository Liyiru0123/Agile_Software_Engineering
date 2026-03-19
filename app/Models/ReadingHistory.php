<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    use HasFactory;

    // Match database table name
    protected $table = 'reading_history';

    // Composite primary key
    protected $primaryKey = ['user_id', 'article_id'];
    public $incrementing = false;

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'article_id',
        'progress',
        'read_at',
    ];

    // Attribute type casting
    protected $casts = [
        'progress' => 'integer',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Belongs to User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relationship: Belongs to Article model (explicit foreign/primary key specification)
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }

    // Fix: Query logic for composite primary key (critical)
    protected function setKeysForSaveQuery($query)
    {
        if (is_array($this->primaryKey)) {
            foreach ($this->primaryKey as $key) {
                $query->where($key, $this->getAttribute($key));
            }
            return $query;
        } else {
            return parent::setKeysForSaveQuery($query);
        }
    }

    // Supplement: Fix delete/find issues with composite primary key
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    public function getKey()
    {
        return collect($this->primaryKey)->map(function ($key) {
            return $this->getAttribute($key);
        })->all();
    }
}