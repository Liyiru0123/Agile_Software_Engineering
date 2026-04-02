@extends('layouts.app')

@section('title', $article->title.' - Article Detail')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-7xl mx-auto px-6">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            Back to Library
        </a>

        <div class="grid lg:grid-cols-[minmax(0,1.5fr)_380px] gap-8 items-start">
            <section class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8 lg:p-10">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="px-3 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs font-semibold">
                        {{ $difficultyLabel }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-[#C9A961]/15 text-[#6B3D2E] text-xs font-semibold">
                        {{ number_format($article->word_count) }} words
                    </span>
                    <span class="px-3 py-1 rounded-full bg-[#4A2C2A]/10 text-[#4A2C2A] text-xs font-semibold">
                        {{ $estimatedMinutes }} min read
                    </span>
                    <span class="px-3 py-1 rounded-full {{ $audioUrl ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} text-xs font-semibold">
                        {{ $audioUrl ? 'Source audio available' : 'No source audio yet' }}
                    </span>
                </div>

                <h1 class="text-4xl font-bold text-[#4A2C2A] mb-4 leading-tight">{{ $article->title }}</h1>
                <p class="text-[#6B3D2E] text-lg leading-8 mb-8">{{ $articleSummary }}</p>

                @if($audioUrl)
                    <div class="mb-8 p-4 rounded-2xl bg-[#FAF4EC] border border-[#E8D9C9]">
                        <div class="text-sm text-[#6B3D2E] font-semibold mb-2">Article Audio</div>
                        <audio controls class="w-full">
                            <source src="{{ $audioUrl }}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                @endif

                <div class="space-y-6 text-[#3A2A22] leading-8 text-[17px]" data-translate-scope="true" data-article-id="{{ $article->id }}" data-source-language="en" data-target-language="zh-CN">
                    @foreach($paragraphs as $paragraph)
                        <p data-article-id="{{ $article->id }}" data-paragraph-index="{{ $loop->index }}">{{ $paragraph }}</p>
                    @endforeach
                </div>
            </section>

            <aside class="space-y-5 sticky top-24">
                <div class="bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-lg">
                    <div class="text-xs uppercase tracking-[0.25em] text-[#D7BE8A] mb-3">Training Hub</div>
                    <h2 class="text-2xl font-bold mb-2">Choose a skill to train</h2>
                    <p class="text-sm text-[#F5E6D3]/80 leading-6">
                        Start with the full article, then switch into listening, speaking, reading, or writing practice. The listening page already supports AI exercise generation, scoring, and speech recognition.
                    </p>
                </div>

                @foreach($trainingCards as $card)
                    <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <h3 class="text-xl font-bold text-[#4A2C2A]">{{ $card['title'] }}</h3>
                            <span class="text-[11px] px-2 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E] font-semibold">
                                {{ $card['status'] }}
                            </span>
                        </div>
                        <p class="text-sm text-[#6B3D2E] leading-6 mb-5">{{ $card['description'] }}</p>
                        <a href="{{ $card['route'] }}"
                           class="inline-flex items-center justify-center w-full rounded-2xl bg-[#6B3D2E] hover:bg-[#4A2C2A] text-white px-4 py-3 font-semibold transition">
                            {{ $card['cta'] }}
                        </a>
                    </div>
                @endforeach
            </aside>
        </div>
    </div>
</div>
@endsection
