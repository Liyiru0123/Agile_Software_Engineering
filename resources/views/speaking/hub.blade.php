@extends('layouts.app')

@section('title', 'Speaking Hub')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-[1440px] mx-auto px-5 md:px-8 xl:px-12">
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-[#4A2C2A]">Speaking Hub</h1>
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
