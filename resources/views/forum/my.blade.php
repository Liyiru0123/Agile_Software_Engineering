@extends('layouts.app')

@section('title', 'My Forum')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-7xl px-6 space-y-8">
        <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Forum Hub</div>
                    <h1 class="mt-2 text-4xl font-black tracking-tight text-[#4A2C2A]">My Forum</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-7 text-[#6B3D2E]">
                        Review your forum activity and jump straight back to your discussions.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('forum.index') }}" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                        Open Forum
                    </a>
                    <a href="{{ route('forum.saved') }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                        Saved Posts
                    </a>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-[#4A2C2A]">Forum Activity</h2>
                    <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">
                        New comments and replies are highlighted directly on the matching post or comment card.
                    </p>
                </div>
                <a href="{{ route('forum.index') }}" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                    Open Forum
                </a>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <section class="rounded-[1.75rem] border border-[#E6D3BC] bg-[#FFFDF9] p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-2xl font-black text-[#4A2C2A]">My Posts</h3>
                        <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $forumPostsSummary['count'] }} total</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse($forumPostsSummary['recent'] as $post)
                            <a href="{{ $post['url'] }}" class="relative block rounded-[1.5rem] border border-[#E6D3BC] bg-white px-5 py-4 transition hover:border-[#C9A961] hover:bg-[#FFF8F0]">
                                @if(($post['unread_count'] ?? 0) > 0)
                                    <span class="absolute right-4 top-4 flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-[#D35D47] px-1.5 text-[11px] font-bold text-white">
                                        {{ min(99, $post['unread_count']) }}
                                    </span>
                                @endif
                                <div class="flex items-center gap-3">
                                    <div class="text-xl font-black text-[#4A2C2A]">{{ $post['title'] }}</div>
                                    @if(($post['unread_count'] ?? 0) > 0)
                                        <span class="h-2.5 w-2.5 rounded-full bg-[#D35D47]"></span>
                                    @endif
                                </div>
                                <div class="mt-2 text-sm text-[#8B6B47]">
                                    Tag: {{ $post['tag_name'] ?? 'Public Forum' }} - {{ $post['created_at'] }}
                                </div>
                                @if(($post['unread_count'] ?? 0) > 0)
                                    <div class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-[#D35D47]">
                                        {{ min(99, $post['unread_count']) }} new comment{{ ($post['unread_count'] ?? 0) > 1 ? 's' : '' }}
                                    </div>
                                @endif
                            </a>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-white px-5 py-8 text-sm text-[#8B6B47]">
                                You have not posted in the forum yet.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-[#E6D3BC] bg-[#FFFDF9] p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-2xl font-black text-[#4A2C2A]">My Comments</h3>
                        <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $forumCommentsSummary['count'] }} total</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse($forumCommentsSummary['recent'] as $comment)
                            <a href="{{ $comment['url'] }}" class="relative block rounded-[1.5rem] border border-[#E6D3BC] bg-white px-5 py-4 transition hover:border-[#C9A961] hover:bg-[#FFF8F0]">
                                @if(($comment['unread_count'] ?? 0) > 0)
                                    <span class="absolute right-4 top-4 flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-[#D35D47] px-1.5 text-[11px] font-bold text-white">
                                        {{ min(99, $comment['unread_count']) }}
                                    </span>
                                @endif
                                <div class="flex items-center gap-3">
                                    <div class="text-base font-black text-[#4A2C2A]">{{ $comment['post_title'] }}</div>
                                    @if(($comment['unread_count'] ?? 0) > 0)
                                        <span class="h-2.5 w-2.5 rounded-full bg-[#D35D47]"></span>
                                    @endif
                                </div>
                                <div class="mt-2 text-sm leading-7 text-[#6B3D2E] forum-preview-clamp">{{ $comment['excerpt'] }}</div>
                                <div class="mt-3 text-sm text-[#8B6B47]">{{ $comment['created_at'] }}</div>
                                @if(($comment['unread_count'] ?? 0) > 0)
                                    <div class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-[#D35D47]">
                                        {{ min(99, $comment['unread_count']) }} new repl{{ ($comment['unread_count'] ?? 0) > 1 ? 'ies' : 'y' }}
                                    </div>
                                @endif
                            </a>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-white px-5 py-8 text-sm text-[#8B6B47]">
                                You have not commented in the forum yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-[#4A2C2A]">Saved Forum Posts</h2>
                    <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">
                        Jump back into discussions you bookmarked for later review.
                    </p>
                </div>
                <a href="{{ route('forum.saved') }}" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                    Browse Forum
                </a>
            </div>

            <div class="mt-6 rounded-[1.75rem] border border-[#E6D3BC] bg-[#FFFDF9] p-6">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-2xl font-black text-[#4A2C2A]">Saved Posts</h3>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $forumSavedSummary['count'] }} total</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse($forumSavedSummary['recent'] as $post)
                        <a href="{{ $post['url'] }}" class="block rounded-[1.5rem] border border-[#E6D3BC] bg-white px-5 py-4 transition hover:border-[#C9A961] hover:bg-[#FFF8F0]">
                            <div class="text-xl font-black text-[#4A2C2A]">{{ $post['title'] }}</div>
                            <div class="mt-2 text-sm text-[#8B6B47]">
                                Tag: {{ $post['tag_name'] ?? 'Public Forum' }} - By {{ $post['author_name'] }} - Saved {{ $post['saved_at'] }}
                            </div>
                        </a>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-white px-5 py-8 text-sm text-[#8B6B47]">
                            You have not saved any forum posts yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
