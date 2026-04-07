@extends('layouts.app')

@section('title', 'Forum - EAPlus')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-8">
    <div class="mx-auto max-w-[1500px] px-6">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <div class="inline-flex items-center rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#8B6B47]">
                    Learning Forum
                </div>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-[#4A2C2A]">Explore posts, ideas, and study reflections.</h1>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-[#8B6B47]">
                    <span>{{ $posts->total() }} posts found</span>
                    @if($selectedScope === 'tags')
                        <span>&middot; {{ $selectedTags->count() }} tags selected</span>
                    @elseif($selectedTag)
                        <span>&middot; #{{ $selectedTag->name }}</span>
                    @elseif($sort === 'most_liked')
                        <span>&middot; Like Ranking</span>
                    @endif
                    @if($author === 'mine')
                        <span>&middot; My Posts</span>
                    @elseif($author === 'participated')
                        <span>&middot; Commented By Me</span>
                    @endif
                    @if($timeframe === '7d')
                        <span>&middot; Last 7 Days</span>
                    @elseif($timeframe === '30d')
                        <span>&middot; Last 30 Days</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('forum.tags.create') }}" class="rounded-2xl border border-[#D9C7B5] bg-white px-5 py-3 text-sm font-semibold text-[#6B3D2E] shadow-sm transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                    Create Tag
                </a>
                <a href="{{ route('forum.posts.create', $selectedTag ? ['tag' => $selectedTag->slug] : []) }}" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#6B3D2E]">
                    Create New Post
                </a>
            </div>
        </div>

        @if(session('forum_status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('forum_status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid items-start gap-8 lg:grid-cols-[320px_minmax(0,1fr)]">
            <aside class="space-y-6 lg:sticky lg:top-24">
                <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-[#4A2C2A]">Tags</h2>
                        <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $tags->count() }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($tags as $tag)
                            <div class="rounded-xl border border-[#E6D3BC] bg-[#FBF7F1] px-4 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <a href="{{ route('forum.index', ['tag' => $tag->slug]) }}" class="min-w-0 flex-1">
                                        <div class="font-semibold text-[#4A2C2A] hover:text-[#6B3D2E]">#{{ $tag->name }}</div>
                                        <div class="mt-1 text-xs text-[#8B6B47]">{{ $tag->posts_count }} posts &middot; by {{ $tag->user?->name ?? 'Unknown' }}</div>
                                        @if($tag->description)
                                            <div class="mt-2 text-xs leading-5 text-[#8B6B47]">{{ $tag->description }}</div>
                                        @endif
                                    </a>

                                    @if(($tag->slug ?? null) !== 'public-forum' && (auth()->user()->is_admin || auth()->id() === $tag->user_id))
                                        @include('forum.partials.inline-delete', [
                                            'id' => 'delete-tag-'.$tag->id,
                                            'action' => route('forum.tags.destroy', $tag),
                                            'message' => 'Delete this tag? Posts under it will be moved to Public Forum.',
                                            'buttonText' => 'Delete',
                                            'confirmText' => 'Delete Tag',
                                            'summaryClass' => 'text-xs font-semibold text-red-600 transition hover:text-red-700 list-none cursor-pointer',
                                            'panelClass' => 'absolute right-0 top-full z-20 mt-3 w-[min(22rem,calc(100vw-2rem))] rounded-[1.5rem] border border-[#E6D3BC] bg-white p-4 shadow-xl',
                                        ])
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-[#D8C3A6] bg-white/70 px-4 py-8 text-center text-sm text-[#8B6B47]">
                                No tags yet. Create one to start a focused discussion board.
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>

            <section class="space-y-6">
                <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-5 shadow-sm">
                    <form method="GET" action="{{ route('forum.index') }}" class="space-y-5">
                        @if($selectedScope === 'default' && empty($selectedTagIds))
                            <input type="hidden" name="scope" value="default">
                        @endif

                        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.35fr)_220px_220px_auto]">
                            <div>
                                <label for="forum-search" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Search Posts</label>
                                <input id="forum-search" name="search" type="text" value="{{ $search }}" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="Search by title or content">
                            </div>

                            <div>
                                <label for="forum-sort" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Sort By</label>
                                <select id="forum-sort" name="sort" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none">
                                    <option value="latest" @selected($sort === 'latest')>Latest</option>
                                    <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
                                    <option value="most_liked" @selected($sort === 'most_liked')>Most Liked</option>
                                    <option value="most_commented" @selected($sort === 'most_commented')>Most Commented</option>
                                    <option value="most_viewed" @selected($sort === 'most_viewed')>Most Viewed</option>
                                </select>
                            </div>

                            <div>
                                <label for="forum-author" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Post Scope</label>
                                <select id="forum-author" name="author" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none">
                                    <option value="all" @selected($author === 'all')>Everyone</option>
                                    <option value="mine" @selected($author === 'mine')>My Posts</option>
                                    <option value="participated" @selected($author === 'participated')>Commented By Me</option>
                                </select>
                            </div>

                            <div class="flex items-end gap-3">
                                <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                                    Apply
                                </button>

                                @if($search !== '' || $sort !== 'latest' || $author !== 'all' || $selectedScope !== 'all' || ! empty($selectedTagIds) || $timeframe !== 'all')
                                    <a href="{{ route('forum.index') }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <label class="block text-sm font-semibold text-[#4A2C2A]">Post Time Range</label>
                                <span class="text-xs text-[#8B6B47]">Limit the current results to a recent window</span>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @foreach(['all' => 'All Time', '7d' => 'Last 7 Days', '30d' => 'Last 30 Days'] as $rangeValue => $rangeLabel)
                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 text-sm transition {{ $timeframe === $rangeValue ? 'border-[#4A2C2A] bg-[#4A2C2A] text-white' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#4A2C2A] hover:border-[#C9A961]' }}">
                                        <input type="radio" name="timeframe" value="{{ $rangeValue }}" class="h-4 w-4 border-[#D9C7B5] text-[#4A2C2A] focus:ring-[#6B3D2E]" @checked($timeframe === $rangeValue)>
                                        <span>{{ $rangeLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <label class="block text-sm font-semibold text-[#4A2C2A]">Filter By Tags</label>
                                <span class="text-xs text-[#8B6B47]">Choose one or more tags</span>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @forelse($tags as $tag)
                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 text-sm transition {{ in_array($tag->id, $selectedTagIds, true) ? 'border-[#4A2C2A] bg-[#4A2C2A] text-white' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#4A2C2A] hover:border-[#C9A961]' }}">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="h-4 w-4 rounded border-[#D9C7B5] text-[#4A2C2A] focus:ring-[#6B3D2E]" @checked(in_array($tag->id, $selectedTagIds, true))>
                                        <span>#{{ $tag->name }}</span>
                                    </label>
                                @empty
                                    <div class="text-sm text-[#8B6B47]">No tags yet. Create one to start a focused discussion board.</div>
                                @endforelse
                            </div>
                        </div>

                        @if($search !== '' || $author !== 'all' || ! empty($selectedTagIds) || $timeframe !== 'all')
                            <div class="flex flex-wrap gap-2">
                                @if($search !== '')
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">Search: {{ $search }}</span>
                                @endif
                                @if($author === 'mine')
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">My Posts</span>
                                @elseif($author === 'participated')
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">Commented By Me</span>
                                @endif
                                @if($timeframe === '7d')
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">Last 7 Days</span>
                                @elseif($timeframe === '30d')
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">Last 30 Days</span>
                                @endif
                                @foreach($selectedTags as $tag)
                                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold text-[#6B3D2E]">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </form>
                </section>

                <section class="space-y-4">
                    @forelse($posts as $post)
                        <article class="rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <div class="mb-3 flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">
                                            {{ $post->tag ? '#'.$post->tag->name : 'Public Forum' }}
                                        </span>
                                        <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                            {{ $post->comments_count }} comments
                                        </span>
                                        <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                            {{ $post->view_count }} views
                                        </span>
                                        <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                            {{ $post->likes_count }} likes
                                        </span>
                                        <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                            {{ $post->favorites_count }} saves
                                        </span>
                                        @if($post->is_pinned)
                                            <span class="rounded-full bg-[#4A2C2A] px-3 py-1 text-xs font-semibold text-white">
                                                Pinned
                                            </span>
                                        @endif
                                        @if(($post->attachments_count ?? 0) > 0 || $post->hasLegacyAttachment())
                                            <span class="rounded-full bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">
                                                Photos
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('forum.posts.show', $post) }}" class="block">
                                        <h3 class="forum-title-clamp text-2xl font-black text-[#4A2C2A] transition hover:text-[#6B3D2E]">{!! $post->highlighted_title !!}</h3>
                                    </a>
                                    <p class="forum-preview-clamp mt-3 leading-7 text-[#6B3D2E]">{!! $post->highlighted_excerpt !!}</p>
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        @if($post->can_pin ?? false)
                                            <form method="POST" action="{{ route('forum.posts.pin', $post) }}">
                                                @csrf
                                                <button type="submit" class="rounded-full border px-4 py-2 text-xs font-semibold transition {{ $post->is_pinned ? 'border-[#4A2C2A] bg-[#F3E7D8] text-[#6B3D2E]' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#6B3D2E] hover:border-[#C9A961]' }}">
                                                    {{ $post->is_pinned ? 'Unpin' : 'Pin' }}
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('forum.posts.like', $post) }}">
                                            @csrf
                                            <button type="submit" class="rounded-full border px-4 py-2 text-xs font-semibold transition {{ $post->liked_by_user ? 'border-[#4A2C2A] bg-[#4A2C2A] text-white' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#6B3D2E] hover:border-[#C9A961]' }}">
                                                {{ $post->liked_by_user ? 'Liked' : 'Like' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('forum.posts.favorite', $post) }}">
                                            @csrf
                                            <button type="submit" class="rounded-full border px-4 py-2 text-xs font-semibold transition {{ $post->favorited_by_user ? 'border-[#6B3D2E] bg-[#F3E7D8] text-[#6B3D2E]' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#6B3D2E] hover:border-[#C9A961]' }}">
                                                {{ $post->favorited_by_user ? 'Saved' : 'Save' }}
                                            </button>
                                        </form>
                                    </div>
                                    <div class="mt-4 text-sm text-[#8B6B47]">
                                        By {{ $post->user?->name ?? 'Unknown' }}
                                        @if($post->user?->is_admin)
                                            <span class="font-semibold text-[#6B3D2E]">&middot; Admin</span>
                                        @endif
                                        <span>&middot; {{ optional($post->created_at)->diffForHumans() }}</span>
                                    </div>
                                </div>

                                @if(auth()->user()->is_admin || auth()->id() === $post->user_id)
                                    @include('forum.partials.inline-delete', [
                                        'id' => 'delete-post-'.$post->id,
                                        'action' => route('forum.posts.destroy', $post),
                                        'message' => 'Delete this post and all its comments?',
                                        'buttonText' => 'Delete',
                                        'confirmText' => 'Delete Post',
                                        'summaryClass' => 'rounded-xl border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 list-none cursor-pointer',
                                    ])
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[2rem] border border-dashed border-[#D8C3A6] bg-white/70 px-6 py-10 text-center text-[#8B6B47]">
                            No posts found for this view. Try another keyword, switch the post scope, or select different tags.
                        </div>
                    @endforelse
                </section>

                <div class="pt-2">
                    {{ $posts->links() }}
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
