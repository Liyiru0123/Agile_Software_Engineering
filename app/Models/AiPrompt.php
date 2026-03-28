<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AiPrompt extends Model
{
    protected $fillable = ['type', 'prompt'];
    public $timestamps = false;
}