@extends('layouts.app')

@section('title', 'Notebook')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#4A2C2A]">Notebook</h1>
                <p class="mt-2 text-[#6B3D2E]">Your saved highlights and sentence excerpts, grouped with their source articles.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('notebook.review') }}" class="inline-flex items-center rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6B3D2E] transition">
                    Start Review
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#4A2C2A] hover:bg-white transition">
                    Back to Dashboard
                </a>
            </div>
        </div>

        @if($notes->count() > 0)
            <div class="grid gap-4">
                @foreach($notes as $note)
                    <article class="rounded-3xl border border-[#E0D2C2] bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">
                                        {{ optional($note->created_at)->format('Y-m-d H:i') ?? 'Saved note' }}
                                    </span>
                                    <a href="{{ route('articles.show', $note->article_id) }}" class="text-xs font-semibold text-[#8A654E] hover:text-[#4A2C2A]">
                                        {{ $note->article?->title ?? 'Untitled Article' }}
                                    </a>
                                </div>

                                <h2 class="text-xl font-bold text-[#4A2C2A] break-words">{{ $note->selected_text }}</h2>

                                <div class="mt-4 rounded-2xl bg-[#FBF7F1] border border-[#EEE2D4] p-4">
                                    <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-2">Source Paragraph</div>
                                    <p class="text-sm leading-7 text-[#3A2A22]">{{ $note->paragraph_text }}</p>
                                </div>

                                <div class="mt-4 rounded-2xl bg-sky-50 border border-sky-200 p-4">
                                    <div class="text-xs uppercase tracking-[0.15em] text-sky-800 font-semibold mb-2">Translation</div>
                                    <p class="text-sm leading-7 text-sky-900">{{ $note->translated_text }}</p>
                                </div>
                            </div>

                            <a href="{{ route('articles.show', $note->article_id) }}"
                               class="inline-flex items-center rounded-2xl bg-[#6B3D2E] px-5 py-3 text-sm font-semibold text-white hover:bg-[#4A2C2A] transition">
                                Open Article
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $notes->links() }}
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-[#D9C7B5] bg-white p-10 text-center text-[#6B3D2E]">
                Your notebook is empty. Highlight text in an article and click Save to add excerpts here.
            </div>
        @endif
    </div>
</div>
@endsection
