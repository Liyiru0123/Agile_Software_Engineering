@extends('layouts.app')

@section('title', 'Shop')

@push('styles')
<style>
    #companion-shell {
        display: none !important;
    }

    .shop-summary-grid article > div:first-child > span:first-child {
        color: transparent;
        font-size: 0;
        position: relative;
    }

    .shop-summary-grid article > div:first-child > span:first-child::after {
        content: '';
        display: block;
        width: 1rem;
        height: 1rem;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }

    .shop-summary-grid article:nth-child(1) > div:first-child > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Ccircle cx='12' cy='12' r='8' fill='%23FFF3CF' stroke='%23C9A961' stroke-width='2'/%3E%3Cpath d='M12 8V16' stroke='%23C9A961' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M8 12H16' stroke='%23C9A961' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
    }

    .shop-summary-grid article:nth-child(2) > div:first-child > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Crect x='4' y='5' width='16' height='14' rx='4' stroke='%23C95F43' stroke-width='2'/%3E%3Cpath d='M8 11L11 14L16 9' stroke='%23C95F43' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    }

    .shop-summary-grid article:nth-child(3) > div:first-child > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M12 4C9 8 8 10 8 13C8 16.3 10.7 19 14 19C17.3 19 20 16.3 20 13C20 10.6 18.8 8.5 16 6C15.6 8.7 14.2 10 12 11C11.8 8.6 11.7 7 12 4Z' fill='%23FBE4DB' stroke='%23C95F43' stroke-width='1.7' stroke-linejoin='round'/%3E%3C/svg%3E");
    }

    .shop-summary-grid article:nth-child(4) > div:first-child > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Crect x='5' y='4' width='14' height='16' rx='4' stroke='%236B3D2E' stroke-width='2'/%3E%3Cpath d='M8 9H16' stroke='%236B3D2E' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M8 13H13' stroke='%23D88C5A' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
    }
</style>
@endpush

@section('content')
@php
    $attendance = $attendanceSummary ?? [];
    $consumables = collect($shopItems)->where('type', 'consumable')->values();
    $outfits = collect($shopItems)->where('type', 'outfit')->values();
    $items = collect($shopItems)->where('type', 'item')->values();
    $shopIcon = function (array $item): string {
        $slug = (string) ($item['slug'] ?? '');
        $type = (string) ($item['type'] ?? 'item');

        return match (true) {
            $slug === 'makeup-checkin-card' => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><rect x="8" y="10" width="32" height="28" rx="8" fill="#FFF8F0" stroke="#C95F43" stroke-width="3"/><path d="M16 18H32" stroke="#C95F43" stroke-width="3" stroke-linecap="round"/><path d="M16 25H27" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/><circle cx="31.5" cy="28.5" r="5.5" fill="#FBE4DB" stroke="#C95F43" stroke-width="2.5"/></svg>',
            $slug === 'double-gold-ticket' => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><rect x="8" y="12" width="32" height="24" rx="8" fill="#FFF8F0" stroke="#D4B970" stroke-width="3"/><circle cx="24" cy="24" r="8" fill="#FFF3CF"/><path d="M24 19V29" stroke="#D4B970" stroke-width="3.5" stroke-linecap="round"/><path d="M19 24H29" stroke="#D4B970" stroke-width="3.5" stroke-linecap="round"/></svg>',
            $type === 'outfit' => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><path d="M18 10L24 16L30 10L36 16V36H12V16L18 10Z" fill="#FFF8F0" stroke="#8B6B47" stroke-width="3" stroke-linejoin="round"/><path d="M20 18L24 22L28 18" stroke="#D88C5A" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            default => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><rect x="11" y="9" width="26" height="30" rx="7" fill="#FFF8F0" stroke="#6B3D2E" stroke-width="3"/><path d="M18 18H30" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><path d="M18 24H30" stroke="#D4B970" stroke-width="3" stroke-linecap="round"/><path d="M18 30H26" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/></svg>',
        };
    };
    $summaryIcon = function (string $key): string {
        return match ($key) {
            'gold' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" fill="#FFF3CF" stroke="#C9A961" stroke-width="2"/><path d="M12 8V16" stroke="#C9A961" stroke-width="2" stroke-linecap="round"/><path d="M8 12H16" stroke="#C9A961" stroke-width="2" stroke-linecap="round"/></svg>',
            'checkin' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="14" rx="4" stroke="#C95F43" stroke-width="2"/><path d="M8 11L11 14L16 9" stroke="#C95F43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'streak' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 4C9 8 8 10 8 13C8 16.3 10.7 19 14 19C17.3 19 20 16.3 20 13C20 10.6 18.8 8.5 16 6C15.6 8.7 14.2 10 12 11C11.8 8.6 11.7 7 12 4Z" fill="#FBE4DB" stroke="#C95F43" stroke-width="1.7" stroke-linejoin="round"/></svg>',
            default => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><rect x="5" y="4" width="14" height="16" rx="4" stroke="#6B3D2E" stroke-width="2"/><path d="M8 9H16" stroke="#6B3D2E" stroke-width="2" stroke-linecap="round"/><path d="M8 13H13" stroke="#D88C5A" stroke-width="2" stroke-linecap="round"/></svg>',
        };
    };
@endphp

<div class="min-h-screen bg-[#F6F0E8] py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 sm:p-8 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-end">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#8B6B47]">
                        Shop
                    </div>
                    <h1 class="mt-4 text-3xl font-black tracking-tight text-[#4A2C2A] sm:text-4xl">
                        Spend gold, manage consumables, and keep your daily sign-ins clean.
                    </h1>
                    <p class="mt-3 text-sm leading-7 text-[#8B6B47] sm:text-base">
                        The old companion scene has been replaced with a focused shop. Sign in here each day, repair missed dates with makeup cards, and buy utility items with gold earned from study modules.
                    </p>
                    <div class="shop-summary-grid mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]"><span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FFF3CF]">●</span>Gold</div>
                            <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ number_format($profile->gold) }}</div>
                        </article>
                        <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]"><span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FBE4DB]">✓</span>Check-Ins</div>
                            <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $attendance['claimed_count'] ?? 0 }}</div>
                        </article>
                        <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]"><span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#E8D6C7]">↗</span>Streak</div>
                            <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $attendance['streak'] ?? 0 }}</div>
                        </article>
                        <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]"><span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FFF3CF]">↺</span>Makeup Cards</div>
                            <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $attendance['makeup_card_quantity'] ?? 0 }}</div>
                        </article>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-[#E8D9C7] bg-[linear-gradient(180deg,#FFF8F0_0%,#F3E4D8_100%)] p-5">
                    <svg class="w-full" viewBox="0 0 280 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="16" y="18" width="248" height="160" rx="24" fill="#FFFDF8" stroke="#D8C3A6" stroke-width="3"/>
                        <rect x="34" y="36" width="72" height="108" rx="18" fill="#FBE4DB"/>
                        <rect x="46" y="50" width="48" height="16" rx="8" fill="#C95F43"/>
                        <path d="M54 101L70 85L86 101L70 117L54 101Z" fill="#FFF8F0"/>
                        <rect x="118" y="36" width="72" height="108" rx="18" fill="#FFF3CF"/>
                        <rect x="130" y="50" width="48" height="16" rx="8" fill="#D4B970"/>
                        <circle cx="154" cy="102" r="18" fill="#FFF8F0"/>
                        <path d="M154 92V112" stroke="#D4B970" stroke-width="6" stroke-linecap="round"/>
                        <path d="M144 102H164" stroke="#D4B970" stroke-width="6" stroke-linecap="round"/>
                        <rect x="202" y="36" width="42" height="108" rx="18" fill="#E8D6C7"/>
                        <rect x="211" y="50" width="24" height="16" rx="8" fill="#6B3D2E"/>
                        <rect x="210" y="90" width="26" height="42" rx="13" fill="#FFF8F0"/>
                        <rect x="52" y="162" width="176" height="14" rx="7" fill="#4A2C2A"/>
                    </svg>
                    <div class="mt-3 text-sm leading-6 text-[#6B3D2E]">
                        Clear visuals mean less guessing: sign in at the top, then scan consumables first, permanent items second.
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_360px]">
            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 sm:p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-[#4A2C2A]">Monthly Check-In</h2>
                        <p class="mt-1 text-sm text-[#8B6B47]">{{ $attendance['current_month_label'] ?? now()->format('F Y') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('shop.check-in') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center rounded-xl bg-[#4A2C2A] px-4 py-3 text-sm font-semibold text-[#F5E6D3] transition hover:bg-[#6B3D2E]"
                                    @disabled($attendance['today_claimed'] ?? false)>
                                {{ ($attendance['today_claimed'] ?? false) ? 'Already Checked In' : 'Check In Today (+'.$dailyRewardAmount.')' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('shop.check-in.makeup') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center rounded-xl border border-[#D8C3A6] px-4 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:bg-[#F9EFE2]"
                                    @disabled(($attendance['makeup_card_quantity'] ?? 0) < 1 || empty($attendance['next_missed_date']))>
                                Use Makeup Card
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-[#E8D9C7] bg-white/80 px-4 py-4 text-sm leading-7 text-[#6B3D2E]">
                    <div>Today reward: <strong>+{{ $dailyRewardAmount }} gold</strong></div>
                    <div>Next missed date you can repair: <strong>{{ $attendance['next_missed_date'] ?? 'None this month' }}</strong></div>
                </div>

                <div class="mt-5 grid grid-cols-7 gap-2 text-center text-xs font-bold uppercase tracking-[0.12em] text-[#A58A6A]">
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayLabel)
                        <div class="py-2">{{ $dayLabel }}</div>
                    @endforeach
                </div>

                <div class="mt-2 grid grid-cols-7 gap-2">
                    @foreach($attendance['days'] ?? [] as $day)
                        @php
                            $tone = match ($day['status']) {
                                'claimed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                'makeup' => 'border-amber-200 bg-amber-50 text-amber-700',
                                'missed' => 'border-red-200 bg-red-50 text-red-700',
                                'today' => 'border-[#D8C3A6] bg-[#F6EBDD] text-[#6B3D2E]',
                                default => 'border-[#E8D9C7] bg-white text-[#8B6B47]',
                            };
                        @endphp
                        <div class="min-h-[84px] rounded-2xl border p-2 text-left {{ $tone }}">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-sm font-black">{{ $day['day'] }}</span>
                                @if($day['is_today'])
                                    <span class="rounded-full bg-[#4A2C2A] px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.12em] text-[#F5E6D3]">Today</span>
                                @endif
                            </div>
                            <div class="mt-4 text-[11px] font-semibold uppercase tracking-[0.08em]">
                                {{ str_replace('_', ' ', $day['status']) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <aside class="space-y-6">
                <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 sm:p-6 shadow-sm">
                    <h2 class="text-xl font-black text-[#4A2C2A]">How This Shop Works</h2>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-[#6B3D2E]">
                        <div class="rounded-2xl bg-white/80 px-4 py-3">1. Earn gold by completing listening, reading, writing, and speaking modules.</div>
                        <div class="rounded-2xl bg-white/80 px-4 py-3">2. Visit this page each day to claim your sign-in reward manually.</div>
                        <div class="rounded-2xl bg-white/80 px-4 py-3">3. Buy makeup cards if you want to repair a missed day later in the month.</div>
                    </div>
                </article>

                <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 sm:p-6 shadow-sm">
                    <h2 class="text-xl font-black text-[#4A2C2A]">Recent Gold Activity</h2>
                    <div class="mt-4 space-y-3">
                        @forelse($transactions as $transaction)
                            <div class="rounded-2xl border border-[#E8D9C7] bg-white/80 px-4 py-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-semibold text-[#4A2C2A]">{{ ucwords($transaction['source']) }}</div>
                                        <div class="mt-1 text-xs text-[#8B6B47]">{{ $transaction['created_at'] }}</div>
                                    </div>
                                    <div class="text-sm font-black {{ $transaction['amount'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $transaction['amount'] >= 0 ? '+' : '' }}{{ $transaction['amount'] }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-[#D8C3A6] bg-white/70 px-4 py-5 text-sm text-[#8B6B47]">
                                No transactions yet.
                            </div>
                        @endforelse
                    </div>
                </article>
            </aside>
        </section>

        <section class="mt-8 space-y-8">
            <div>
                <h2 class="text-2xl font-black text-[#4A2C2A]">Consumables</h2>
                <p class="mt-1 text-sm text-[#8B6B47]">Utility items can stack. Makeup cards work immediately from this page.</p>
            </div>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($consumables as $item)
                    @php
                        $visual = $item['visual'] ?? [];
                        $surface = $visual['surface'] ?? '#F9EFE2';
                        $accent = $visual['accent'] ?? '#8B6B47';
                    @endphp
                    <article class="rounded-[2rem] border border-[#E2CFB3] p-5 shadow-sm" style="background-color: {{ $surface }};">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-white/80 shadow-sm">{!! $shopIcon($item) !!}</div>
                                <h3 class="mt-3 text-xl font-black text-[#4A2C2A]">{{ $item['name'] }}</h3>
                                <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">{{ $item['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] uppercase tracking-[0.14em] text-[#8B6B47]">Price</div>
                                <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $item['price'] }}</div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-[#8B6B47]">
                            <span class="rounded-full border border-[#D9C6A8] px-2.5 py-1">{{ ucfirst($item['rarity']) }}</span>
                            <span class="rounded-full border border-[#D9C6A8] px-2.5 py-1">Qty {{ $item['quantity'] }}</span>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('shop.purchase', $item['id']) }}">
                                @csrf
                                <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-4 py-3 text-sm font-semibold text-[#F5E6D3] transition hover:bg-[#6B3D2E]">
                                    Buy Again
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="mt-8 space-y-8">
            <div>
                <h2 class="text-2xl font-black text-[#4A2C2A]">Permanent Items</h2>
                <p class="mt-1 text-sm text-[#8B6B47]">Outfits and room items stay in your collection after one purchase.</p>
            </div>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($outfits as $item)
                    @php
                        $visual = $item['visual'] ?? [];
                        $surface = $visual['surface'] ?? '#F9EFE2';
                        $accent = $visual['accent'] ?? '#8B6B47';
                    @endphp
                    <article class="rounded-[2rem] border border-[#E2CFB3] p-5 shadow-sm" style="background-color: {{ $surface }};">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-white/80 shadow-sm">{!! $shopIcon($item) !!}</div>
                                <h3 class="mt-3 text-xl font-black text-[#4A2C2A]">{{ $item['name'] }}</h3>
                                <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">{{ $item['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] uppercase tracking-[0.14em] text-[#8B6B47]">Price</div>
                                <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $item['price'] }}</div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-[#8B6B47]">
                            <span class="rounded-full border border-[#D9C6A8] px-2.5 py-1">{{ ucfirst($item['rarity']) }}</span>
                            @if($item['owned'])
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">Owned</span>
                            @endif
                            @if($item['equipped'])
                                <span class="rounded-full border border-[#C9A961] bg-[#FFF5DB] px-2.5 py-1 text-[#8B6B47]">Equipped</span>
                            @endif
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            @if(! $item['owned'])
                                <form method="POST" action="{{ route('shop.purchase', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-4 py-3 text-sm font-semibold text-[#F5E6D3] transition hover:bg-[#6B3D2E]">
                                        Buy Outfit
                                    </button>
                                </form>
                            @elseif(! $item['equipped'])
                                <form method="POST" action="{{ route('shop.equip', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="rounded-2xl border border-[#C9A961] bg-[#FFF8ED] px-4 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:bg-[#F6EAD8]">
                                        Equip
                                    </button>
                                </form>
                            @else
                                <div class="rounded-2xl border border-[#D8C3A6] bg-white/70 px-4 py-3 text-sm font-semibold text-[#8B6B47]">
                                    Currently equipped
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
                @foreach($items as $item)
                    @php
                        $visual = $item['visual'] ?? [];
                        $surface = $visual['surface'] ?? '#F9EFE2';
                        $accent = $visual['accent'] ?? '#8B6B47';
                    @endphp
                    <article class="rounded-[2rem] border border-[#E2CFB3] p-5 shadow-sm" style="background-color: {{ $surface }};">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-white/80 shadow-sm">{!! $shopIcon($item) !!}</div>
                                <h3 class="mt-3 text-xl font-black text-[#4A2C2A]">{{ $item['name'] }}</h3>
                                <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">{{ $item['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] uppercase tracking-[0.14em] text-[#8B6B47]">Price</div>
                                <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $item['price'] }}</div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-[#8B6B47]">
                            <span class="rounded-full border border-[#D9C6A8] px-2.5 py-1">{{ ucfirst($item['rarity']) }}</span>
                            @if($item['owned'])
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">Owned</span>
                            @endif
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            @if(! $item['owned'])
                                <form method="POST" action="{{ route('shop.purchase', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-4 py-3 text-sm font-semibold text-[#F5E6D3] transition hover:bg-[#6B3D2E]">
                                        Buy Item
                                    </button>
                                </form>
                            @else
                                <div class="rounded-2xl border border-[#D8C3A6] bg-white/70 px-4 py-3 text-sm font-semibold text-[#8B6B47]">
                                    Already collected
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection
