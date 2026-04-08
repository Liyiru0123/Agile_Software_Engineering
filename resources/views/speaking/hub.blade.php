@extends('layouts.app')

@section('title', 'Speaking Hub')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8 mb-8">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-3 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs font-semibold">Speaking Hub</span>
                <span class="px-3 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E] text-xs font-semibold">Choose a mode</span>
            </div>

            <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">Speaking</h1>
            <p class="text-[#6B3D2E] leading-7 max-w-3xl">
                Choose the speaking mode you want to use. You can continue with article-based speaking practice, or open the reserved AI conversation page for the future Live2D integration.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            @foreach($entryCards as $card)
                <section class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8 flex flex-col">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h2 class="text-2xl font-bold text-[#4A2C2A]">{{ $card['title'] }}</h2>
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
