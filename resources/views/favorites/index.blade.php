@extends('layouts.app')

@section('title', 'Favorites')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#4A2C2A]">Favorites</h1>
                <p class="mt-2 text-[#6B3D2E]">All articles you have saved for later reading and practice.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('favorites.plan') }}" class="inline-flex items-center rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6B3D2E] transition">
                    Generate Plan from Favorites
                </a>
                <a href="{{ route('articles.index', ['favorites' => 1]) }}" class="inline-flex items-center rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#4A2C2A] hover:bg-white transition">
                    Open in Library
                </a>
            </div>
        </div>

        @if($favorites->count() > 0)
            <div class="grid md:grid-cols-2 gap-5">
                @foreach($favorites as $article)
                    <article class="rounded-3xl border border-[#E0D2C2] bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">
                                {{ match((int) $article->difficulty) { 1 => 'Foundation', 2 => 'Intermediate', 3 => 'Advanced', default => 'General' } }}
                            </span>
                            <span class="text-xs text-[#9A7358]">
                                Saved {{ \Illuminate\Support\Carbon::parse($article->favorited_at)->diffForHumans() }}
                            </span>
                        </div>

                        <h2 class="text-2xl font-bold text-[#4A2C2A]">{{ $article->title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-[#6B3D2E]">
                            {{ \Illuminate\Support\Str::limit($article->content, 180) }}
                        </p>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ route('articles.show', $article->id) }}" class="inline-flex items-center rounded-2xl bg-[#6B3D2E] px-4 py-3 text-sm font-semibold text-white hover:bg-[#4A2C2A] transition">
                                Open Article
                            </a>
                            <a href="{{ route('articles.listening', $article->id) }}" class="inline-flex items-center rounded-2xl border border-[#D9C7B5] px-4 py-3 text-sm font-semibold text-[#4A2C2A] hover:bg-[#FBF7F1] transition">
                                Start Practice
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-[#D9C7B5] bg-white p-10 text-center text-[#6B3D2E]">
                No favorite articles yet. Save some articles from the library first.
            </div>
        @endif
    </div>
</div>
@endsection
