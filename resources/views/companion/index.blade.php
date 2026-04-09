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
    $achievements = collect($achievements ?? []);
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
    $badgeIcon = function (array $achievement): string {
        $key = (string) ($achievement['key'] ?? '');

        return match (true) {
            str_contains($key, 'listening') => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 10h3l4-4v12l-4-4H4v-4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M15 9c1.4 1.4 1.4 4.6 0 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M18 7c2.6 2.6 2.6 7.4 0 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            str_contains($key, 'checkin_streak_30') => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 3c-2 3-3 4.5-3 7a6 6 0 1 0 12 0c0-2-1-3.5-3-6-1 2-2 3-4 4-.2-1.5-.2-2.5-2-5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M7 20h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            str_contains($key, 'checkin_streak_7') => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 3c-2 3-3 4.5-3 7a6 6 0 1 0 12 0c0-2-1-3.5-3-6-1 2-2 3-4 4-.2-1.5-.2-2.5-2-5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            default => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.6-4.8 2.6.9-5.4L4.2 8.7l5.4-.8L12 3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
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
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-black text-[#4A2C2A]">Titles</h2>
            </div>
            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($achievements as $achievement)
                    @php($unlocked = (bool) ($achievement['unlocked'] ?? false))
                    <div class="rounded-2xl border px-4 py-4 transition {{ $unlocked ? 'border-[#6B3D2E] bg-[#6B3D2E] text-[#FDF7EE] shadow-md' : 'border-[#E2D2BF] bg-[#F7EFE4] text-[#7F6B57]' }}">
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full {{ $unlocked ? 'bg-[#F6D8A8] text-[#4A2C2A]' : 'bg-[#EFE4D5] text-[#A99580]' }}">
                            {!! $badgeIcon($achievement) !!}
                        </div>
                        <div class="mt-2 text-sm font-bold leading-6">{{ $achievement['name'] }}</div>
                        <div class="mt-1 text-xs leading-5 opacity-85">{{ $achievement['description'] }}</div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-[#D8C3A6] bg-[#FBF4EA] px-4 py-4 text-sm text-[#8B6B47]">
                        No titles yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="mt-8 rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 sm:p-8 shadow-sm">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h2 class="text-2xl font-black text-[#4A2C2A]">Badge Wall</h2>
                    <p class="mt-1 text-sm text-[#8B6B47]">Collect badges to light up your wall. Unowned badges can be purchased here.</p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse($items as $item)
                    @php($isOwned = (bool) ($item['owned'] ?? false))
                    <article class="rounded-2xl border px-3 py-3 transition {{ $isOwned ? 'border-[#D4B970] bg-[#FFF6DC] text-[#4A2C2A] shadow-sm' : 'border-[#E2D2BF] bg-[#F7EFE4] text-[#8B6B47]' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/85 {{ $isOwned ? 'shadow-sm' : 'opacity-65' }}">
                                {!! $shopIcon($item) !!}
                            </div>
                            <div class="text-right leading-none">
                                <div class="text-[10px] uppercase tracking-[0.12em] text-[#8B6B47]">Price</div>
                                <div class="mt-1 text-lg font-black text-[#4A2C2A]">{{ $item['price'] }}</div>
                            </div>
                        </div>

                        <h3 class="mt-2 text-sm font-black leading-5">{{ $item['name'] }}</h3>

                        <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[11px]">
                            <span class="rounded-full border border-[#D9C6A8] px-2 py-0.5">{{ ucfirst($item['rarity']) }}</span>
                            @if($isOwned)
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-emerald-700">Lit</span>
                            @endif
                        </div>

                        <div class="mt-3">
                            @if(! $isOwned)
                                <form method="POST" action="{{ route('shop.purchase', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] transition hover:bg-[#6B3D2E]">
                                        Buy Badge
                                    </button>
                                </form>
                            @elseif(! $item['equipped'])
                                <form method="POST" action="{{ route('shop.equip', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-[#C9A961] bg-[#FFF8ED] px-3 py-2 text-xs font-semibold text-[#6B3D2E] transition hover:bg-[#F6EAD8]">
                                        Wear Badge Style
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('shop.unequip') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-[#C95F43] bg-[#FBE4DB] px-3 py-2 text-xs font-semibold text-[#7A2F1F] transition hover:bg-[#F6D4C7]">
                                        Remove Badge Style
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-[#D8C3A6] bg-[#FBF4EA] px-4 py-4 text-sm text-[#8B6B47]">
                        No badges available.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="mt-8 space-y-8">
            <div>
                <h2 class="text-2xl font-black text-[#4A2C2A]">Consumables</h2>
                <p class="mt-1 text-sm text-[#8B6B47]">Utility items can stack. Makeup cards work immediately from this page.</p>
            </div>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($consumables as $item)
                    <article class="rounded-[2rem] border border-[#E2CFB3] p-5 shadow-sm" style="background-color: {{ data_get($item, 'visual.surface', '#F9EFE2') }};">
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
                    <article class="rounded-[2rem] border border-[#E2CFB3] p-5 shadow-sm" style="background-color: {{ data_get($item, 'visual.surface', '#F9EFE2') }};">
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
            </div>
        </section>
    </div>
</div>
@endsection
