<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanionShopItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'type',
        'description',
        'price',
        'rarity',
        'visual',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'visual' => 'array',
            'is_active' => 'bool',
        ];
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(CompanionInventory::class, 'shop_item_id');
    }
}
