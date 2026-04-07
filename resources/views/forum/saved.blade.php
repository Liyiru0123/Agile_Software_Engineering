@extends('layouts.app')

@section('title', 'Saved Forum Posts - EAPlus')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-[1300px] px-6">
        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="inline-flex items-center rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#8B6B47]">
                    Saved Discussions
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-[#4A2C2A]">Your saved forum posts.</h1>
                <p class="mt-3 max-w-3xl leading-7 text-[#8B6B47]">
                    Revisit helpful explanations, strong ideas, and discussions you want to review again later.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('forum.index') }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                    Back to Forum
                </a>
            </div>
        </div>

        @if(session('forum_status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('forum_status') }}
            </div>
        @endif

        <section class="space-y-4">
            <div class="rounded-[2rem] border border-[#E6D3BC] bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('forum.saved') }}" class="space-y-5">
                    <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_240px_auto]">
                        <div>
                            <label for="saved-search" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Search Saved Posts</label>
                            <input id="saved-search" name="search" type="text" value="{{ $search }}" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="Search by title or content">
                        </div>

                        <div>
                            <label for="saved-sort" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Sort By</label>
                            <select id="saved-sort" name="sort" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none">
                                <option value="saved_latest" @selected($sort === 'saved_latest')>Recently Saved</option>
                                <option value="saved_oldest" @selected($sort === 'saved_oldest')>Oldest Saved</option>
                                <option value="most_liked" @selected($sort === 'most_liked')>Most Liked</option>
                                <option value="most_viewed" @selected($sort === 'most_viewed')>Most Viewed</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                                Apply
                            </button>
                            @if($search !== '' || $sort !== 'saved_latest' || ! empty($selectedTagIds))
                                <a href="{{ route('forum.saved') }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <label class="block text-sm font-semibold text-[#4A2C2A]">Filter By Tags</label>
                            <span class="text-xs text-[#8B6B47]">Show only saved posts under selected tags</span>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            @forelse($tags as $tag)
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 text-sm transition {{ in_array($tag->id, $selectedTagIds, true) ? 'border-[#4A2C2A] bg-[#4A2C2A] text-white' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#4A2C2A] hover:border-[#C9A961]' }}">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="h-4 w-4 rounded border-[#D9C7B5] text-[#4A2C2A] focus:ring-[#6B3D2E]" @checked(in_array($tag->id, $selectedTagIds, true))>
                                    <span>#{{ $tag->name }}</span>
                                </label>
                            @empty
                                <div class="text-sm text-[#8B6B47]">No tags available yet.</div>
                            @endforelse
                        </div>
                    </div>
                </form>
            </div>

            @forelse($posts as $post)
                <article class="rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="mb-3 flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">
                                    {{ $post->tag ? '#'.$post->tag->name : 'Public Forum' }}
                                </span>
                                <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                    {{ $post->likes_count }} likes
                                </span>
                                <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                    {{ $post->comments_count }} comments
                                </span>
                                <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                    {{ $post->view_count }} views
                                </span>
                                @if(($post->attachments_count ?? 0) > 0 || $post->hasLegacyAttachment())
                                    <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                        Photos
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('forum.posts.show', $post) }}" class="block">
                                <h2 class="forum-title-clamp text-2xl font-black text-[#4A2C2A] transition hover:text-[#6B3D2E]">{!! $post->highlighted_title !!}</h2>
                            </a>
                            <p class="forum-preview-clamp mt-3 leading-7 text-[#6B3D2E]">{!! $post->highlighted_excerpt !!}</p>
                            <div class="mt-4 text-sm text-[#8B6B47]">
                                By {{ $post->user?->name ?? 'Unknown' }}
                                &middot; Saved {{ optional($post->pivot?->created_at)->diffForHumans() }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('forum.posts.favorite', $post) }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-[#6B3D2E] bg-[#F3E7D8] px-4 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:bg-[#ead7c0]">
                                Remove Save
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-[2rem] border border-dashed border-[#D8C3A6] bg-white/70 px-6 py-10 text-center text-[#8B6B47]">
                    No saved forum posts yet. Save a post from the forum and it will appear here.
                </div>
            @endforelse

            <div class="pt-2">
                {{ $posts->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
