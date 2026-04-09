<?php

namespace App\Services;

use App\Models\CompanionInventory;
use App\Models\CompanionProfile;
use App\Models\CompanionShopItem;
use App\Models\CompanionTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompanionService
{
    protected const DAILY_LOGIN_REWARD = 25;

    protected const MODULE_REWARDS = [
        'listening' => 12,
        'reading' => 10,
        'writing' => 15,
        'speaking' => 18,
    ];

    public function ensureProfile(User|int $user): CompanionProfile
    {
        $userId = $user instanceof User ? $user->id : $user;

        return CompanionProfile::query()->firstOrCreate(
            ['user_id' => $userId],
            [
                'gold' => 0,
                'total_gold_earned' => 0,
            ]
        );
    }

    public function grantDailyLoginReward(User|int $user): array
    {
        $today = now()->toDateString();

        return $this->grantCoins(
            userId: $user instanceof User ? $user->id : $user,
            amount: self::DAILY_LOGIN_REWARD,
            source: 'daily_login',
            rewardKey: 'daily-login:'.$today,
            meta: ['date' => $today]
        );
    }

    public function grantLearningReward(User|int $user, string $module, int $articleId): array
    {
        $amount = self::MODULE_REWARDS[$module] ?? 0;

        if ($amount <= 0) {
            return ['awarded' => false, 'amount' => 0];
        }

        return $this->grantCoins(
            userId: $user instanceof User ? $user->id : $user,
            amount: $amount,
            source: $module.'_complete',
            rewardKey: 'module:'.$module.':article:'.$articleId,
            meta: [
                'module' => $module,
                'article_id' => $articleId,
            ]
        );
    }

    public function grantCoins(int $userId, int $amount, string $source, ?string $rewardKey = null, array $meta = []): array
    {
        $profile = $this->ensureProfile($userId);

        return DB::transaction(function () use ($profile, $amount, $source, $rewardKey, $meta) {
            if ($rewardKey) {
                $existing = CompanionTransaction::query()
                    ->where('user_id', $profile->user_id)
                    ->where('reward_key', $rewardKey)
                    ->first();

                if ($existing) {
                    return [
                        'awarded' => false,
                        'amount' => 0,
                        'gold' => $profile->fresh()->gold,
                        'message' => 'Reward already claimed.',
                    ];
                }
            }

            CompanionTransaction::query()->create([
                'user_id' => $profile->user_id,
                'type' => 'earn',
                'source' => $source,
                'amount' => $amount,
                'reward_key' => $rewardKey,
                'meta' => $meta,
            ]);

            $profile->increment('gold', $amount);
            $profile->increment('total_gold_earned', $amount);

            if ($source === 'daily_login') {
                $profile->forceFill(['last_daily_reward_at' => now()])->save();
            }

            return [
                'awarded' => true,
                'amount' => $amount,
                'gold' => $profile->fresh()->gold,
                'message' => 'Reward granted.',
            ];
        });
    }

    public function purchaseItem(User|int $user, CompanionShopItem $item): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        $profile = $this->ensureProfile($userId);

        if (! $item->is_active) {
            return ['success' => false, 'message' => 'This item is currently unavailable.'];
        }

        $alreadyOwned = CompanionInventory::query()
            ->where('user_id', $userId)
            ->where('shop_item_id', $item->id)
            ->exists();

        if ($alreadyOwned) {
            return ['success' => false, 'message' => 'You already own this item.'];
        }

        if ($profile->gold < $item->price) {
            return [
                'success' => false,
                'message' => 'Not enough gold.',
                'missing_gold' => $item->price - $profile->gold,
            ];
        }

        DB::transaction(function () use ($profile, $userId, $item) {
            CompanionInventory::query()->create([
                'user_id' => $userId,
                'shop_item_id' => $item->id,
                'purchased_at' => now(),
            ]);

            CompanionTransaction::query()->create([
                'user_id' => $userId,
                'type' => 'spend',
                'source' => 'shop_purchase',
                'amount' => -1 * (int) $item->price,
                'reward_key' => null,
                'meta' => [
                    'shop_item_id' => $item->id,
                    'slug' => $item->slug,
                ],
            ]);

            $profile->decrement('gold', $item->price);

            if ($item->type === 'outfit' && ! $profile->equipped_shop_item_id) {
                $profile->forceFill(['equipped_shop_item_id' => $item->id])->save();
            }
        });

        return [
            'success' => true,
            'message' => 'Item purchased successfully.',
            'gold' => $profile->fresh()->gold,
        ];
    }

    public function equipItem(User|int $user, CompanionShopItem $item): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($item->type !== 'outfit' && $item->type !== 'item') {
            return ['success' => false, 'message' => 'Only outfits and badge items can be equipped.'];
        }

        $ownsItem = CompanionInventory::query()
            ->where('user_id', $userId)
            ->where('shop_item_id', $item->id)
            ->exists();

        if (! $ownsItem) {
            return ['success' => false, 'message' => 'Buy this item before equipping it.'];
        }

        $profile = $this->ensureProfile($userId);
        $profile->forceFill(['equipped_shop_item_id' => $item->id])->save();

        return [
            'success' => true,
            'message' => 'Item equipped.',
            'equipped_item_id' => $item->id,
        ];
    }

    public function unequipItem(User|int $user): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        $profile = $this->ensureProfile($userId);

        if (! $profile->equipped_shop_item_id) {
            return [
                'success' => false,
                'message' => 'No item is currently equipped.',
            ];
        }

        $profile->forceFill(['equipped_shop_item_id' => null])->save();

        return [
            'success' => true,
            'message' => 'Item removed.',
        ];
    }

    public function getPagePayload(User $user): array
    {
        $profile = $this->ensureProfile($user)->load('equippedItem');
        $ownedItemIds = CompanionInventory::query()
            ->where('user_id', $user->id)
            ->pluck('shop_item_id')
            ->all();

        $shopItems = CompanionShopItem::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('price')
            ->get()
            ->map(function (CompanionShopItem $item) use ($ownedItemIds, $profile) {
                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'name' => $item->name,
                    'type' => $item->type,
                    'description' => $item->description,
                    'price' => $item->price,
                    'rarity' => $item->rarity,
                    'visual' => $item->visual ?? [],
                    'owned' => in_array($item->id, $ownedItemIds, true),
                    'equipped' => (int) $profile->equipped_shop_item_id === (int) $item->id,
                ];
            })
            ->values()
            ->all();

        $transactions = CompanionTransaction::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->take(8)
            ->get()
            ->map(fn (CompanionTransaction $transaction) => [
                'type' => $transaction->type,
                'source' => str_replace('_', ' ', $transaction->source),
                'amount' => $transaction->amount,
                'created_at' => optional($transaction->created_at)->diffForHumans(),
            ])
            ->all();

        $claimedToday = CompanionTransaction::query()
            ->where('user_id', $user->id)
            ->where('reward_key', 'daily-login:'.now()->toDateString())
            ->exists();

        return [
            'profile' => $profile,
            'shopItems' => $shopItems,
            'transactions' => $transactions,
            'claimedDailyReward' => $claimedToday,
            'dailyRewardAmount' => self::DAILY_LOGIN_REWARD,
        ];
    }
}
