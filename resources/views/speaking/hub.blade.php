@extends('layouts.app')

@section('title', 'Speaking Hub')

@push('styles')
<style>
    .speaking-chip-row > span > span:first-child {
        color: transparent;
        font-size: 0;
        position: relative;
    }

    .speaking-chip-row > span > span:first-child::after {
        content: '';
        display: block;
        width: 1rem;
        height: 1rem;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }

    .speaking-chip-row > span:nth-child(1) > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M4 8H20' stroke='%23C95F43' stroke-width='2.2' stroke-linecap='round'/%3E%3Cpath d='M7 12H17' stroke='%23C95F43' stroke-width='2.2' stroke-linecap='round'/%3E%3Cpath d='M10 16H14' stroke='%23C95F43' stroke-width='2.2' stroke-linecap='round'/%3E%3C/svg%3E");
    }

    .speaking-chip-row > span:nth-child(2) > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M6 12L10 16L18 8' stroke='%23C9A961' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
            <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="px-3 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs font-semibold">Speaking Hub</span>
                    <span class="px-3 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E] text-xs font-semibold">Choose a mode</span>
                </div>

                <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">Speaking</h1>
                <p class="text-[#6B3D2E] leading-7 max-w-3xl">
                    Choose the speaking mode you want to use. You can continue with article-based speaking practice, talk with the Live2D AI companion, or enter the video call room.
                </p>

                <div class="speaking-chip-row mt-6 flex flex-wrap gap-3 text-sm text-[#8B6B47]">
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#FBF6EF] px-3 py-2"><span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FBE4DB]">●</span>Practice by mode</span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#FBF6EF] px-3 py-2"><span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FFF3CF]">◌</span>Clear visual choices</span>
                </div>
            </div>

            <div class="rounded-3xl border border-[#E0D2C2] bg-[linear-gradient(180deg,#FFF8F0_0%,#F3E4D8_100%)] p-5 shadow-sm">
                <svg class="w-full" viewBox="0 0 260 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="132" cy="88" r="58" fill="#F3E7D8"/>
                    <rect x="48" y="134" width="164" height="46" rx="23" fill="#4A2C2A"/>
                    <rect x="88" y="64" width="88" height="68" rx="26" fill="#FFFDF8" stroke="#D8C3A6" stroke-width="3"/>
                    <rect x="120" y="38" width="24" height="32" rx="12" fill="#D88C5A"/>
                    <rect x="106" y="118" width="52" height="38" rx="19" fill="#FFF3CF"/>
                    <path d="M132 84V116" stroke="#6B3D2E" stroke-width="8" stroke-linecap="round"/>
                    <path d="M117 96H147" stroke="#6B3D2E" stroke-width="8" stroke-linecap="round"/>
                </svg>
                <div class="mt-3 text-sm leading-6 text-[#6B3D2E]">
                    Start with the picture, then choose the mode. The page should explain itself before the user reads everything.
                </div>
            </div>
        </div>

        <div class="mt-8 grid lg:grid-cols-2 gap-6">
            @foreach($entryCards as $card)
                <section class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8 flex flex-col">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-[#FBF6EF]">
                                @if($card['title'] === 'Article Speaking Learning')
                                    <svg class="h-9 w-9" viewBox="0 0 48 48" fill="none"><rect x="10" y="8" width="28" height="32" rx="7" fill="#FFF8F0" stroke="#6B3D2E" stroke-width="3"/><path d="M17 17H31" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><path d="M17 23H31" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/><path d="M17 29H26" stroke="#C9A961" stroke-width="3" stroke-linecap="round"/></svg>
                                @elseif($card['title'] === 'AI Conversation')
                                    <svg class="h-9 w-9" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="20" r="10" fill="#FFF3CF" stroke="#6B3D2E" stroke-width="3"/><path d="M14 36C17 31 21 29 24 29C27 29 31 31 34 36" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><path d="M12 18H8" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/><path d="M40 18H36" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/></svg>
                                @else
                                    <svg class="h-9 w-9" viewBox="0 0 48 48" fill="none"><rect x="9" y="12" width="30" height="22" rx="7" fill="#FFF8F0" stroke="#6B3D2E" stroke-width="3"/><path d="M24 34V41" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><path d="M15 41H33" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><circle cx="24" cy="23" r="5" fill="#D88C5A"/></svg>
                                @endif
                            </div>
                            <h2 class="text-2xl font-bold text-[#4A2C2A]">{{ $card['title'] }}</h2>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E] text-xs font-semibold">
                            {{ $card['status'] }}
                        </span>
                    </div>
                    <p class="text-[#6B3D2E] leading-7 mb-6 flex-1">{{ $card['description'] }}</p>
                    <a href="{{ $card['route'] }}"
                       class="inline-flex items-center justify-center rounded-2xl bg-[#6B3D2E] hover:bg-[#4A2C2A] text-white px-5 py-3 font-semibold transition">
                        {{ $card['cta'] }}
                    </a>
                </section>
            @endforeach
        </div>
    </div>
</div>
@endsection
