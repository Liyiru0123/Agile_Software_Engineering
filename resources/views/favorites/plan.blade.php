@extends('layouts.app')

@section('title', 'Plan From Favorites')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-5xl mx-auto px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#4A2C2A]">Generate Plan from Favorites</h1>
                <p class="mt-2 text-[#6B3D2E]">Select saved articles and assign them to a study date.</p>
            </div>
            <a href="{{ route('favorites.index') }}" class="inline-flex items-center rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#4A2C2A] hover:bg-white transition">
                Back to Favorites
            </a>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if($favorites->count() > 0)
            <form method="POST" action="{{ route('favorites.plan.store') }}" class="rounded-[2rem] border border-[#E0D2C2] bg-white p-8 shadow-sm">
                @csrf

                <div class="mb-6">
                    <label for="plan_date" class="block text-sm font-semibold text-[#4A2C2A] mb-2">Plan Date</label>
                    <input
                        id="plan_date"
                        name="plan_date"
                        type="date"
                        value="{{ old('plan_date', now()->toDateString()) }}"
                        class="w-full max-w-xs rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#4A2C2A] focus:outline-none focus:border-[#6B3D2E]"
                    >
                    @error('plan_date')
                        <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-4">
                    @foreach($favorites as $article)
                        <label class="flex items-start gap-4 rounded-3xl border border-[#E7D8C8] bg-[#FBF7F1] p-5 cursor-pointer hover:border-[#C9A961] transition">
                            <input
                                type="checkbox"
                                name="article_ids[]"
                                value="{{ $article->id }}"
                                class="mt-1 h-5 w-5 rounded border-[#C9A961] text-[#6B3D2E] focus:ring-[#C9A961]"
                                {{ in_array($article->id, old('article_ids', []), true) ? 'checked' : '' }}
                            >
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <div class="text-lg font-bold text-[#4A2C2A]">{{ $article->title }}</div>
                                    @if(in_array($article->id, $plannedArticleIds, true))
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                            Already planned before
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm leading-6 text-[#6B3D2E]">
                                    {{ \Illuminate\Support\Str::limit($article->content, 180) }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('article_ids')
                    <div class="mt-4 text-sm text-red-600">{{ $message }}</div>
                @enderror

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#4A2C2A] px-6 py-3 text-sm font-semibold text-white hover:bg-[#6B3D2E] transition">
                        Create Study Plan
                    </button>
                </div>
            </form>
        @else
            <div class="rounded-3xl border border-dashed border-[#D9C7B5] bg-white p-10 text-center text-[#6B3D2E]">
                No favorite articles yet. Add articles to favorites before creating a study plan.
            </div>
        @endif
    </div>
</div>
@endsection
