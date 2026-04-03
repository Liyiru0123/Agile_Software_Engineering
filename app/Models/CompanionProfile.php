<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanionProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gold',
        'total_gold_earned',
        'equipped_shop_item_id',
        'last_daily_reward_at',
    ];

    protected function casts(): array
    {
        return [
            'last_daily_reward_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equippedItem(): BelongsTo
    {
        return $this->belongsTo(CompanionShopItem::class, 'equipped_shop_item_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CompanionTransaction::class, 'user_id', 'user_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(CompanionInventory::class, 'user_id', 'user_id');
    }
}
