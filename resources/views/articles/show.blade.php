@extends('layouts.app')

@section('title', $article->title.' - Article Detail')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-[1600px] mx-auto px-6">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            Back to Library
        </a>

        <div class="grid lg:grid-cols-[minmax(0,1.85fr)_400px] gap-8 items-start">
            <section class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8 lg:p-10 lg:h-[calc(100vh-5.5rem)] lg:flex lg:flex-col">
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

                <h1 class="text-4xl font-bold text-[#4A2C2A] mb-3 leading-tight">{{ $article->title }}</h1>
                <p class="text-[#6B3D2E] text-lg leading-8 mb-5">{{ $articleSummary }}</p>

                <div class="mb-5 rounded-2xl bg-[#FBF7F1] border border-[#E8D9C9] p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="text-xs uppercase tracking-[0.18em] text-[#9A7358] font-semibold">Quick Start</div>
                        <div class="text-xs text-[#8A654E]">Open a training page instantly</div>
                    </div>

                    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3">
                        @foreach($trainingCards as $card)
                            <a href="{{ $card['route'] }}"
                               class="rounded-2xl border border-[#E4D5C6] bg-white px-4 py-4 hover:border-[#6B3D2E] hover:shadow-sm transition">
                                <div class="text-sm font-bold text-[#4A2C2A]">{{ $card['title'] }}</div>
                                <div class="text-xs text-[#8A654E] mt-1">{{ $card['status'] }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($audioUrl)
                    <div class="mb-5 p-4 rounded-2xl bg-[#FAF4EC] border border-[#E8D9C9]">
                        <div class="text-sm text-[#6B3D2E] font-semibold mb-2">Article Audio</div>
                        <audio controls class="w-full">
                            <source src="{{ $audioUrl }}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                @endif

                <div class="space-y-6 text-[#3A2A22] leading-8 text-[17px] lg:flex-1 lg:min-h-0 lg:overflow-y-auto lg:pr-3 article-body-scroll"
                     data-translate-scope="true"
                     data-article-id="{{ $article->id }}"
                     data-source-language="en"
                     data-target-language="zh-CN">
                    @foreach($articleParagraphs as $paragraph)
                        <section class="rounded-3xl border border-[#EEE2D4] bg-[#FCF8F2] px-5 py-4">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <span class="inline-flex items-center rounded-full bg-[#4A2C2A] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-white">
                                    Paragraph {{ $paragraph['display_index'] }}
                                </span>
                                @if(!empty($paragraph['time_range_label']))
                                    <span class="inline-flex items-center rounded-full bg-[#F3E7D8] px-3 py-1 text-[11px] font-semibold text-[#6B3D2E]">
                                        {{ $paragraph['time_range_label'] }}
                                    </span>
                                @endif
                            </div>
                            <p data-article-id="{{ $article->id }}" data-paragraph-index="{{ $loop->index }}">{{ $paragraph['text'] }}</p>
                        </section>
                    @endforeach
                </div>
            </section>

            <aside class="space-y-5 sticky top-24 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto lg:pr-2 training-hub-scroll">
                <div class="bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-lg">
                    <div class="text-xs uppercase tracking-[0.25em] text-[#D7BE8A] mb-3">Training Hub</div>
                    <h2 class="text-2xl font-bold mb-2">Choose a skill to train</h2>
                    <p class="text-sm text-[#F5E6D3]/80 leading-6">
                        Start with the full article, then move into listening, reading, speaking, or writing practice.
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

@push('styles')
<style>
    .article-body-scroll,
    .training-hub-scroll {
        scrollbar-width: thin;
        scrollbar-color: #c9a961 #f6f0e8;
    }

    .article-body-scroll::-webkit-scrollbar,
    .training-hub-scroll::-webkit-scrollbar {
        width: 10px;
    }

    .article-body-scroll::-webkit-scrollbar-track,
    .training-hub-scroll::-webkit-scrollbar-track {
        background: #f6f0e8;
        border-radius: 999px;
    }

    .article-body-scroll::-webkit-scrollbar-thumb,
    .training-hub-scroll::-webkit-scrollbar-thumb {
        background: #c9a961;
        border-radius: 999px;
        border: 2px solid #f6f0e8;
    }
</style>
@endpush
