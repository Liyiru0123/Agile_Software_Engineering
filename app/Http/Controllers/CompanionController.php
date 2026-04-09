<?php

namespace App\Http\Controllers;

use App\Models\CompanionInventory;
use App\Models\CompanionShopItem;
use App\Models\CompanionTransaction;
use App\Models\DailyAttendance;
use App\Models\Submission;
use App\Services\CompanionService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanionController extends Controller
{
    protected const DAILY_CHECKIN_REWARD = 25;

    public function __construct(
        protected CompanionService $companionService
    ) {
    }

    public function index(): View
    {
        return view('companion.index', $this->buildShopPayload(request()->user()));
    }

    public function purchase(CompanionShopItem $item): RedirectResponse
    {
        $user = request()->user();
        $profile = $this->companionService->ensureProfile($user);

        if (! $item->is_active) {
            $result = ['success' => false, 'message' => 'This item is currently unavailable.'];
        } else {
            $existingInventory = CompanionInventory::query()
                ->where('user_id', $user->id)
                ->where('shop_item_id', $item->id)
                ->first();

            if ($existingInventory && ! $item->stackable) {
                $result = ['success' => false, 'message' => 'You already own this item.'];
            } elseif ($profile->gold < $item->price) {
                $result = ['success' => false, 'message' => 'Not enough gold.'];
            } else {
                DB::transaction(function () use ($profile, $user, $item, $existingInventory) {
                    if ($existingInventory) {
                        $existingInventory->increment('quantity');
                        $existingInventory->forceFill([
                            'purchased_at' => now(),
                        ])->save();
                    } else {
                        CompanionInventory::query()->create([
                            'user_id' => $user->id,
                            'shop_item_id' => $item->id,
                            'quantity' => 1,
                            'purchased_at' => now(),
                        ]);
                    }

                    CompanionTransaction::query()->create([
                        'user_id' => $user->id,
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

                $result = [
                    'success' => true,
                    'message' => $item->stackable ? 'Item quantity increased.' : 'Item purchased successfully.',
                ];
            }
        }

        return redirect()
            ->route('shop.index')
            ->with($result['success'] ? 'status' : 'error', $result['message']);
    }

    public function equip(CompanionShopItem $item): RedirectResponse
    {
        $result = $this->companionService->equipItem(request()->user(), $item);

        return redirect()
            ->route('shop.index')
            ->with($result['success'] ? 'status' : 'error', $result['message']);
    }

    public function unequip(): RedirectResponse
    {
        $result = $this->companionService->unequipItem(request()->user());

        return redirect()
            ->route('shop.index')
            ->with($result['success'] ? 'status' : 'error', $result['message']);
    }

    public function checkIn(): RedirectResponse
    {
        $user = request()->user();
        $today = now()->startOfDay();

        $alreadyCheckedIn = DailyAttendance::query()
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyCheckedIn) {
            return redirect()
                ->back()
                ->with('error', 'Today has already been checked in.');
        }

        $reward = $this->companionService->grantCoins(
            userId: $user->id,
            amount: self::DAILY_CHECKIN_REWARD,
            source: 'daily_check_in',
            rewardKey: 'check-in:'.$today->toDateString(),
            meta: ['date' => $today->toDateString()]
        );

        DailyAttendance::query()->create([
            'user_id' => $user->id,
            'attendance_date' => $today->toDateString(),
            'source' => 'claim',
            'reward_amount' => self::DAILY_CHECKIN_REWARD,
            'shop_item_id' => null,
        ]);

        return redirect()
            ->back()
            ->with('status', 'Today checked in successfully. +'.$reward['amount'].' gold.');
    }

    public function useMakeupCard(): RedirectResponse
    {
        $user = request()->user();
        $card = CompanionShopItem::query()->where('benefit_key', 'makeup_checkin')->first();

        if (! $card) {
            return redirect()->back()->with('error', 'Makeup card item is unavailable.');
        }

        $inventory = CompanionInventory::query()
            ->where('user_id', $user->id)
            ->where('shop_item_id', $card->id)
            ->first();

        if (! $inventory || $inventory->quantity < 1) {
            return redirect()->back()->with('error', 'You do not have any makeup check-in cards.');
        }

        $targetDate = $this->resolveLatestMissedDate($user->id);
        if (! $targetDate) {
            return redirect()->back()->with('error', 'There is no missed day available to repair this month.');
        }

        DB::transaction(function () use ($user, $card, $inventory, $targetDate) {
            DailyAttendance::query()->create([
                'user_id' => $user->id,
                'attendance_date' => $targetDate->toDateString(),
                'source' => 'makeup',
                'reward_amount' => 0,
                'shop_item_id' => $card->id,
            ]);

            if ($inventory->quantity <= 1) {
                $inventory->delete();
            } else {
                $inventory->decrement('quantity');
                $inventory->forceFill(['last_used_at' => now()])->save();
            }

            CompanionTransaction::query()->create([
                'user_id' => $user->id,
                'type' => 'use',
                'source' => 'makeup_check_in_card',
                'amount' => 0,
                'reward_key' => null,
                'meta' => [
                    'attendance_date' => $targetDate->toDateString(),
                    'shop_item_id' => $card->id,
                ],
            ]);
        });

        return redirect()
            ->back()
            ->with('status', 'One missed sign-in day has been repaired.');
    }

    protected function buildShopPayload($user): array
    {
        $profile = $this->companionService->ensureProfile($user)->load('equippedItem');
        $inventory = CompanionInventory::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('shop_item_id');

        $shopItems = CompanionShopItem::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('price')
            ->get()
            ->map(function (CompanionShopItem $item) use ($inventory, $profile) {
                $inventoryItem = $inventory->get($item->id);

                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'name' => $item->name,
                    'type' => $item->type,
                    'stackable' => (bool) $item->stackable,
                    'description' => $item->description,
                    'price' => $item->price,
                    'rarity' => $item->rarity,
                    'benefit_key' => $item->benefit_key,
                    'visual' => $item->visual ?? [],
                    'owned' => $inventoryItem !== null,
                    'quantity' => (int) ($inventoryItem?->quantity ?? 0),
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

        return [
            'profile' => $profile,
            'shopItems' => $shopItems,
            'transactions' => $transactions,
            'attendanceSummary' => $this->buildAttendanceSummary($user),
            'achievements' => $this->buildAchievements($user->id),
            'dailyRewardAmount' => self::DAILY_CHECKIN_REWARD,
        ];
    }

    protected function buildAttendanceSummary($user): array
    {
        $today = now()->startOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $attendance = DailyAttendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(fn (DailyAttendance $record) => $record->attendance_date->toDateString());

        $days = [];
        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            $dateString = $cursor->toDateString();
            $record = $attendance->get($dateString);
            $status = 'upcoming';

            if ($record) {
                $status = $record->source === 'makeup' ? 'makeup' : 'claimed';
            } elseif ($cursor->lt($today)) {
                $status = 'missed';
            } elseif ($cursor->isSameDay($today)) {
                $status = 'today';
            }

            $days[] = [
                'date' => $dateString,
                'day' => $cursor->day,
                'status' => $status,
                'is_today' => $cursor->isSameDay($today),
            ];

            $cursor->addDay();
        }

        $makeupCard = CompanionShopItem::query()->where('benefit_key', 'makeup_checkin')->first();
        $makeupInventory = $makeupCard
            ? CompanionInventory::query()
                ->where('user_id', $user->id)
                ->where('shop_item_id', $makeupCard->id)
                ->first()
            : null;

        return [
            'today_claimed' => $attendance->has($today->toDateString()),
            'current_month_label' => $today->format('F Y'),
            'days' => $days,
            'claimed_count' => $attendance->count(),
            'streak' => $this->calculateCheckInStreak($user->id),
            'next_missed_date' => $this->resolveLatestMissedDate($user->id)?->toDateString(),
            'makeup_card_quantity' => (int) ($makeupInventory?->quantity ?? 0),
        ];
    }

    protected function buildAchievements(int $userId): array
    {
        $currentStreak = $this->calculateCheckInStreak($userId);

        $hasAnyCheckIn = DailyAttendance::query()
            ->where('user_id', $userId)
            ->exists();

        $listeningSubmissionCount = Submission::query()
            ->join('exercises', 'submissions.exercise_id', '=', 'exercises.id')
            ->where('submissions.user_id', $userId)
            ->where('exercises.type', 'listening')
            ->count();

        $totalSubmissionCount = Submission::query()
            ->where('user_id', $userId)
            ->count();

        $perfectListeningCompletedInTime = Submission::query()
            ->join('exercises', 'submissions.exercise_id', '=', 'exercises.id')
            ->where('submissions.user_id', $userId)
            ->where('exercises.type', 'listening')
            ->where('submissions.time_spent', '<=', 1200)
            ->where(function ($query) {
                $query->where('submissions.score', '>=', 99.99)
                    ->orWhereBetween('submissions.score', [0.99, 1.01]);
            })
            ->exists();

        return [
            [
                'key' => 'checkin_once',
                'name' => 'One-Day Check-In',
                'description' => 'Check in at least once.',
                'unlocked' => $hasAnyCheckIn,
            ],
            [
                'key' => 'checkin_streak_3',
                'name' => '3-Day Momentum',
                'description' => 'Check in for 3 consecutive days.',
                'unlocked' => $currentStreak >= 3,
            ],
            [
                'key' => 'checkin_streak_7',
                'name' => '7-Day Check-In Streak',
                'description' => 'Check in for 7 consecutive days.',
                'unlocked' => $currentStreak >= 7,
            ],
            [
                'key' => 'checkin_streak_30',
                'name' => '30-Day Check-In Streak',
                'description' => 'Check in for 30 consecutive days.',
                'unlocked' => $currentStreak >= 30,
            ],
            [
                'key' => 'listening_perfect_speed',
                'name' => 'Speed Listener',
                'description' => 'Complete one listening task within 20 minutes with 100% accuracy.',
                'unlocked' => $perfectListeningCompletedInTime,
            ],
            [
                'key' => 'listening_explorer_5',
                'name' => 'Listening Explorer',
                'description' => 'Complete 5 listening submissions.',
                'unlocked' => $listeningSubmissionCount >= 5,
            ],
            [
                'key' => 'practice_sessions_20',
                'name' => 'Practice Grinder',
                'description' => 'Complete 20 total practice submissions.',
                'unlocked' => $totalSubmissionCount >= 20,
            ],
        ];
    }

    protected function calculateCheckInStreak(int $userId): int
    {
        $records = DailyAttendance::query()
            ->where('user_id', $userId)
            ->orderByDesc('attendance_date')
            ->get();

        if ($records->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $expected = now()->startOfDay();

        if (! $records->contains(fn (DailyAttendance $record) => $record->attendance_date->isSameDay($expected))) {
            $expected = $expected->subDay();
        }

        foreach ($records as $record) {
            if ($record->attendance_date->isSameDay($expected)) {
                $streak++;
                $expected = $expected->copy()->subDay();
                continue;
            }

            if ($record->attendance_date->lt($expected)) {
                break;
            }
        }

        return $streak;
    }

    protected function resolveLatestMissedDate(int $userId): ?CarbonInterface
    {
        $today = now()->startOfDay();
        $monthStart = $today->copy()->startOfMonth();

        if ($today->isSameDay($monthStart)) {
            return null;
        }

        $claimedDates = DailyAttendance::query()
            ->where('user_id', $userId)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $today->copy()->subDay()->toDateString()])
            ->pluck('attendance_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        $cursor = $today->copy()->subDay();
        while ($cursor->gte($monthStart)) {
            if (! in_array($cursor->toDateString(), $claimedDates, true)) {
                return $cursor->copy();
            }

            $cursor->subDay();
        }

        return null;
    }
}
