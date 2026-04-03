@extends('layouts.app')

@section('title', 'Browsing History')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#4A2C2A]">Browsing History</h1>
                <p class="text-[#6B3D2E] mt-2">Review your recent article activity and jump back to the exact practice page you opened last.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('history.continue') }}" class="inline-flex items-center rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6B3D2E] transition">
                    Continue Last Session
                </a>
                <a href="{{ route('articles.index') }}" class="inline-flex items-center rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#4A2C2A] hover:bg-white transition">
                    Back to Library
                </a>
            </div>
        </div>

        @if($history->count() > 0)
            <div class="grid gap-4">
                @foreach($history as $item)
                    <div class="rounded-3xl border border-[#E0D2C2] bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">
                                        {{ $item->page_label }}
                                    </span>
                                    <span class="rounded-full bg-[#4A2C2A]/10 px-3 py-1 text-xs font-semibold text-[#4A2C2A]">
                                        {{ $item->visit_count }} visits
                                    </span>
                                    <span class="text-xs text-[#9A7358]">
                                        Last opened {{ optional($item->last_viewed_at)->diffForHumans() ?? 'just now' }}
                                    </span>
                                </div>
                                <h2 class="text-2xl font-bold text-[#4A2C2A]">{{ $item->article?->title ?? 'Untitled Article' }}</h2>
                                <p class="mt-2 text-sm leading-6 text-[#6B3D2E]">
                                    First viewed: {{ optional($item->first_viewed_at)->format('Y-m-d H:i') ?? '-' }}
                                    <span class="mx-2 text-[#D0B8A0]">|</span>
                                    Last viewed: {{ optional($item->last_viewed_at)->format('Y-m-d H:i') ?? '-' }}
                                </p>
                            </div>
                            <a href="{{ $item->continue_url ?? route('articles.show', $item->article_id) }}"
                               class="inline-flex items-center rounded-2xl bg-[#6B3D2E] px-5 py-3 text-sm font-semibold text-white hover:bg-[#4A2C2A] transition">
                                Continue
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $history->links() }}
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-[#D9C7B5] bg-white p-10 text-center text-[#6B3D2E]">
                No browsing history yet. Open an article to start tracking your reading activity.
            </div>
        @endif
    </div>
</div>
@endsection
