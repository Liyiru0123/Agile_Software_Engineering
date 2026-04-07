@extends('layouts.app')

@section('title', $post->title.' - Forum')

@section('content')
@php
    $editingPost = request()->query('editing') === 'post';
    $editingCommentId = (int) request()->query('editing_comment', 0);
@endphp
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-[1200px] px-6">
        <a href="{{ route('forum.index', ['tag' => $post->tag?->slug ?? $publicTag->slug]) }}"
           class="mb-6 inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A]">
            &larr; Back to Forum
        </a>

        @if(session('forum_status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('forum_status') }}
            </div>
        @endif

        @if(session('social_status'))
            <div class="mb-6 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
                {{ session('social_status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <article class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
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
                    </div>

                    @if($editingPost && auth()->id() === $post->user_id)
                        <form method="POST" action="{{ route('forum.posts.update', $post) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="edit-post-title" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Title</label>
                                <input id="edit-post-title" name="title" type="text" value="{{ old('title', $post->title) }}" maxlength="160" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none">
                            </div>

                            <div>
                                <label for="edit-forum-tag-id" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Tag</label>
                                <select id="edit-forum-tag-id" name="forum_tag_id" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" @selected((int) old('forum_tag_id', $post->forum_tag_id ?: $publicTag->id) === $tag->id)>#{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="edit-post-body" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Content</label>
                                <textarea id="edit-post-body" name="body" rows="8" class="w-full rounded-[1.5rem] border border-[#D9C7B5] px-5 py-4 text-[#3A2A22] leading-7 focus:border-[#6B3D2E] focus:outline-none">{{ old('body', $post->body) }}</textarea>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Photos</label>
                                @include('forum.partials.attachment', ['item' => $post])
                                @if($post->hasAnyAttachments())
                                    <label class="mt-3 inline-flex items-center gap-2 text-sm text-[#6B3D2E]">
                                        <input type="checkbox" name="remove_attachments" value="1" class="rounded border-[#D9C7B5] text-[#4A2C2A] focus:ring-[#6B3D2E]">
                                        <span>Remove current photos</span>
                                    </label>
                                @endif
                                <div class="mt-3">
                                    @include('forum.partials.photo-upload', [
                                        'id' => 'edit-post-attachments',
                                        'name' => 'attachments[]',
                                        'label' => 'Add More Photos',
                                        'buttonLabel' => 'Choose Photos',
                                        'emptyText' => 'No new photos selected',
                                        'helperText' => 'Upload more images to extend the gallery, or tick the remove option to clear the current photos first.',
                                    ])
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                                    Save Changes
                                </button>
                                <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage()]) }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    @else
                        <h1 class="text-4xl font-black tracking-tight text-[#4A2C2A]">{{ $post->title }}</h1>
                        @php
                            $postAuthorAvatar = mb_strtoupper(trim(mb_substr($post->user?->name ?? 'U', 0, 2)));
                            $postSocialState = $socialTargetStates[$post->user_id] ?? ['is_friend' => false, 'pending_sent' => false, 'pending_received' => false];
                        @endphp
                        <div class="mt-5 flex flex-wrap items-center gap-4">
                            <button type="button"
                                    class="forum-social-avatar flex h-14 w-14 items-center justify-center rounded-full border border-[#D9C7B5] bg-[#F3E7D8] text-sm font-black uppercase tracking-[0.08em] text-[#4A2C2A] transition hover:border-[#C9A961] hover:bg-[#EEDBC7]"
                                    data-user-id="{{ $post->user_id }}"
                                    data-user-name="{{ $post->user?->name ?? 'Unknown' }}"
                                    data-source-type="forum_post"
                                    data-source-id="{{ $post->id }}"
                                    data-is-friend="{{ $postSocialState['is_friend'] ? '1' : '0' }}"
                                    data-pending-sent="{{ $postSocialState['pending_sent'] ? '1' : '0' }}"
                                    data-pending-received="{{ $postSocialState['pending_received'] ? '1' : '0' }}"
                                    data-social-mode="menu"
                                    title="Open social actions for {{ $post->user?->name ?? 'this user' }}">
                                {{ $postAuthorAvatar }}
                            </button>
                            <div class="text-sm text-[#8B6B47]">
                                <div class="text-base font-semibold text-[#4A2C2A]">
                                    {{ $post->user?->name ?? 'Unknown' }}
                                    @if($post->user?->is_admin)
                                        <span class="font-semibold text-[#6B3D2E]">&middot; Admin</span>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    {{ optional($post->created_at)->format('Y-m-d H:i') }}
                                    @if($post->updated_at && $post->updated_at->gt($post->created_at))
                                        <span class="font-semibold text-[#6B3D2E]">&middot; Edited</span>
                                    @endif
                                    @if(auth()->id() !== $post->user_id)
                                        <span class="font-semibold text-[#C08A42]">&middot; Click avatar for social actions</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
                    @if($post->can_pin ?? false)
                        <form method="POST" action="{{ route('forum.posts.pin', $post) }}">
                            @csrf
                            <button type="submit" class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $post->is_pinned ? 'border-[#4A2C2A] bg-[#F3E7D8] text-[#6B3D2E]' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#6B3D2E] hover:border-[#C9A961]' }}">
                                {{ $post->is_pinned ? 'Unpin Post' : 'Pin Post' }}
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('forum.posts.like', $post) }}">
                        @csrf
                        <button type="submit" class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $post->liked_by_user ? 'border-[#4A2C2A] bg-[#4A2C2A] text-white' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#6B3D2E] hover:border-[#C9A961]' }}">
                            {{ $post->liked_by_user ? 'Liked' : 'Like' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('forum.posts.favorite', $post) }}">
                        @csrf
                        <button type="submit" class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $post->favorited_by_user ? 'border-[#6B3D2E] bg-[#F3E7D8] text-[#6B3D2E]' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#6B3D2E] hover:border-[#C9A961]' }}">
                            {{ $post->favorited_by_user ? 'Saved' : 'Save' }}
                        </button>
                    </form>

                    @if(auth()->id() === $post->user_id && ! $editingPost)
                        <a href="{{ route('forum.posts.show', ['post' => $post, 'editing' => 'post', 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage()]) }}" class="rounded-xl border border-[#D9C7B5] px-4 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                            Edit Post
                        </a>
                    @endif

                    @if(auth()->user()->is_admin || auth()->id() === $post->user_id)
                        @include('forum.partials.inline-delete', [
                            'id' => 'delete-post-detail-'.$post->id,
                            'action' => route('forum.posts.destroy', $post),
                            'message' => 'Delete this post and all comments?',
                            'buttonText' => 'Delete Post',
                            'confirmText' => 'Delete Post',
                            'summaryClass' => 'rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 list-none cursor-pointer',
                        ])
                    @endif
                </div>
            </div>

            @unless($editingPost && auth()->id() === $post->user_id)
                <div class="forum-content-wrap forum-social-target mt-6 whitespace-pre-line rounded-[1.5rem] bg-[#FBF7F1] px-6 py-5 leading-8 text-[#3A2A22]"
                     data-user-id="{{ $post->user_id }}"
                     data-user-name="{{ $post->user?->name ?? 'Unknown' }}"
                     data-source-type="forum_post"
                     data-source-id="{{ $post->id }}"
                     data-is-friend="{{ $postSocialState['is_friend'] ? '1' : '0' }}"
                     data-pending-sent="{{ $postSocialState['pending_sent'] ? '1' : '0' }}"
                     data-pending-received="{{ $postSocialState['pending_received'] ? '1' : '0' }}">
                    {{ $post->body }}
                </div>
                @include('forum.partials.attachment', ['item' => $post])
            @endunless
        </article>

        <section id="comment-form" class="mt-8 rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <h2 class="mb-5 text-2xl font-black text-[#4A2C2A]">Add a Comment</h2>

            <form method="POST" action="{{ route('forum.comments.store', $post) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="comments" value="{{ $commentSort }}">
                <input type="hidden" name="comment_filter" value="{{ $commentFilter }}">
                @if($replyToComment)
                    <input type="hidden" name="reply_to_comment_id" value="{{ $replyToComment->id }}">
                    <div class="rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8B6B47]">Replying To</div>
                        <div class="mt-2 text-sm font-semibold text-[#4A2C2A]">{{ $replyToComment->user?->name ?? 'Unknown user' }}</div>
                        <div class="mt-2 line-clamp-3 text-sm leading-6 text-[#6B3D2E]">{{ $replyToComment->body }}</div>
                        <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage()]) }}#comment-form" class="mt-3 inline-flex text-xs font-semibold text-[#6B3D2E] hover:text-[#4A2C2A]">
                            Cancel Reply
                        </a>
                    </div>
                @endif
                <textarea name="body" rows="5" class="w-full rounded-[1.5rem] border border-[#D9C7B5] px-5 py-4 leading-7 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="Share your opinion, answer a question, or add a study tip.">{{ old('body') }}</textarea>
                @include('forum.partials.photo-upload', [
                    'id' => 'comment-attachments',
                    'name' => 'attachments[]',
                    'label' => 'Photos',
                    'buttonLabel' => 'Choose Photos',
                    'emptyText' => 'No photos selected',
                    'helperText' => 'Optional. Upload JPG, PNG, GIF, or WEBP images. Maximum 5 MB per image.',
                ])
                <button type="submit" class="rounded-2xl bg-[#6B3D2E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#4A2C2A]">
                    Post Comment
                </button>
            </form>
        </section>

        <section class="mt-8 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-black text-[#4A2C2A]">Comments</h2>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $post->comments_count }}</span>
                    @if($commentFilter === 'author')
                        <span class="rounded-full border border-[#D9C7B5] bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">Only Author</span>
                    @elseif($commentFilter === 'mine')
                        <span class="rounded-full border border-[#D9C7B5] bg-[#FBF7F1] px-3 py-1 text-xs font-semibold text-[#8B6B47]">Only My Replies</span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 rounded-full border border-[#E6D3BC] bg-white px-2 py-2">
                        <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => 'oldest', 'comment_filter' => $commentFilter]) }}#comments" class="rounded-full px-3 py-1 text-xs font-semibold transition {{ $commentSort === 'oldest' ? 'bg-[#4A2C2A] text-white' : 'text-[#6B3D2E] hover:bg-[#FBF7F1]' }}">
                            Oldest First
                        </a>
                        <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => 'latest', 'comment_filter' => $commentFilter]) }}#comments" class="rounded-full px-3 py-1 text-xs font-semibold transition {{ $commentSort === 'latest' ? 'bg-[#4A2C2A] text-white' : 'text-[#6B3D2E] hover:bg-[#FBF7F1]' }}">
                            Latest First
                        </a>
                    </div>
                    <div class="flex items-center gap-2 rounded-full border border-[#E6D3BC] bg-white px-2 py-2">
                        <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => 'all']) }}#comments" class="rounded-full px-3 py-1 text-xs font-semibold transition {{ $commentFilter === 'all' ? 'bg-[#4A2C2A] text-white' : 'text-[#6B3D2E] hover:bg-[#FBF7F1]' }}">
                            All Comments
                        </a>
                        <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => 'author']) }}#comments" class="rounded-full px-3 py-1 text-xs font-semibold transition {{ $commentFilter === 'author' ? 'bg-[#4A2C2A] text-white' : 'text-[#6B3D2E] hover:bg-[#FBF7F1]' }}">
                            Only Author
                        </a>
                        <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => 'mine']) }}#comments" class="rounded-full px-3 py-1 text-xs font-semibold transition {{ $commentFilter === 'mine' ? 'bg-[#4A2C2A] text-white' : 'text-[#6B3D2E] hover:bg-[#FBF7F1]' }}">
                            Only My Replies
                        </a>
                    </div>
                </div>
            </div>

            <div id="comments"></div>
            @forelse($comments as $comment)
                <article id="comment-{{ $comment->id }}" class="scroll-mt-28 rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            @php
                                $commentSocialState = $socialTargetStates[$comment->user_id] ?? ['is_friend' => false, 'pending_sent' => false, 'pending_received' => false];
                                $commentAuthorAvatar = mb_strtoupper(trim(mb_substr($comment->user?->name ?? 'U', 0, 2)));
                            @endphp
                            <div class="flex flex-wrap items-center gap-4 text-sm text-[#8B6B47]">
                                <button type="button"
                                        class="forum-social-avatar forum-social-primary flex h-12 w-12 items-center justify-center rounded-full border border-[#D9C7B5] bg-[#F3E7D8] text-xs font-black uppercase tracking-[0.08em] text-[#4A2C2A] transition hover:border-[#C9A961] hover:bg-[#EEDBC7]"
                                        data-user-id="{{ $comment->user_id }}"
                                        data-user-name="{{ $comment->user?->name ?? 'Unknown' }}"
                                        data-source-type="forum_comment"
                                        data-source-id="{{ $comment->id }}"
                                        data-is-friend="{{ $commentSocialState['is_friend'] ? '1' : '0' }}"
                                        data-pending-sent="{{ $commentSocialState['pending_sent'] ? '1' : '0' }}"
                                        data-pending-received="{{ $commentSocialState['pending_received'] ? '1' : '0' }}"
                                        data-social-mode="primary"
                                        title="Connect with {{ $comment->user?->name ?? 'this user' }}">
                                    {{ $commentAuthorAvatar }}
                                </button>
                                <div>
                                    <div>
                                        {{ $comment->user?->name ?? 'Unknown' }}
                                        @if($comment->user?->is_admin)
                                            <span class="font-semibold text-[#6B3D2E]">&middot; Admin</span>
                                        @endif
                                        @if($comment->is_pinned)
                                            <span class="font-semibold text-[#4A2C2A]">&middot; Pinned</span>
                                        @endif
                                    </div>
                                    <div class="mt-1">
                                        {{ optional($comment->created_at)->format('Y-m-d H:i') }}
                                        @if($comment->updated_at && $comment->updated_at->gt($comment->created_at))
                                            <span class="font-semibold text-[#6B3D2E]">&middot; Edited</span>
                                        @endif
                                        @if(auth()->id() !== $comment->user_id)
                                            <span class="font-semibold text-[#C08A42]">&middot; Click avatar to connect</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($comment->replyParent)
                                <div class="mt-3 rounded-[1.25rem] border border-[#E6D3BC] bg-[#FBF7F1] px-4 py-3">
                                    <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8B6B47]">
                                        Replying to {{ $comment->replyParent->user?->name ?? 'Unknown user' }}
                                    </div>
                                    <div class="mt-2 line-clamp-3 text-sm leading-6 text-[#6B3D2E]">
                                        {{ $comment->replyParent->body }}
                                    </div>
                                </div>
                            @endif

                            @if($editingCommentId === $comment->id && auth()->id() === $comment->user_id)
                                <form method="POST" action="{{ route('forum.comments.update', $comment) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="comments" value="{{ $commentSort }}">
                                    <input type="hidden" name="comment_filter" value="{{ $commentFilter }}">
                                    <input type="hidden" name="page" value="{{ $comments->currentPage() }}">
                                    <textarea name="body" rows="5" class="w-full rounded-[1.5rem] border border-[#D9C7B5] px-5 py-4 leading-7 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none">{{ old('body', $comment->body) }}</textarea>
                                    <div>
                                        @include('forum.partials.attachment', ['item' => $comment])
                                        @if($comment->hasAnyAttachments())
                                            <label class="mt-3 inline-flex items-center gap-2 text-sm text-[#6B3D2E]">
                                                <input type="checkbox" name="remove_attachments" value="1" class="rounded border-[#D9C7B5] text-[#4A2C2A] focus:ring-[#6B3D2E]">
                                                <span>Remove current photos</span>
                                            </label>
                                        @endif
                                        <div class="mt-3">
                                            @include('forum.partials.photo-upload', [
                                                'id' => 'comment-attachments-'.$comment->id,
                                                'name' => 'attachments[]',
                                                'label' => 'Add More Photos',
                                                'buttonLabel' => 'Choose Photos',
                                                'emptyText' => 'No new photos selected',
                                                'helperText' => 'Upload more images to extend the comment gallery, or remove the current photos first.',
                                            ])
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                                            Save Comment
                                        </button>
                                        <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage()]) }}#comment-{{ $comment->id }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            @else
                                <div class="forum-content-wrap forum-social-target mt-3 whitespace-pre-line leading-7 text-[#3A2A22]"
                                     data-user-id="{{ $comment->user_id }}"
                                     data-user-name="{{ $comment->user?->name ?? 'Unknown' }}"
                                     data-source-type="forum_comment"
                                     data-source-id="{{ $comment->id }}"
                                     data-is-friend="{{ $commentSocialState['is_friend'] ? '1' : '0' }}"
                                     data-pending-sent="{{ $commentSocialState['pending_sent'] ? '1' : '0' }}"
                                     data-pending-received="{{ $commentSocialState['pending_received'] ? '1' : '0' }}">{{ $comment->body }}</div>
                                @include('forum.partials.attachment', ['item' => $comment])
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3">
                            @if($comment->can_pin ?? false)
                                <form method="POST" action="{{ route('forum.comments.pin', $comment) }}">
                                    @csrf
                                    <input type="hidden" name="comments" value="{{ $commentSort }}">
                                    <input type="hidden" name="comment_filter" value="{{ $commentFilter }}">
                                    <input type="hidden" name="page" value="{{ $comments->currentPage() }}">
                                    <button type="submit" class="rounded-xl border px-3 py-2 text-sm font-semibold transition {{ $comment->is_pinned ? 'border-[#4A2C2A] bg-[#F3E7D8] text-[#6B3D2E]' : 'border-[#D9C7B5] bg-[#FBF7F1] text-[#6B3D2E] hover:border-[#C9A961]' }}">
                                        {{ $comment->is_pinned ? 'Unpin' : 'Pin' }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage(), 'reply_to' => $comment->id]) }}#comment-form" class="rounded-xl border border-[#D9C7B5] px-3 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                Reply
                            </a>
                            @if(auth()->user()->is_admin || auth()->id() === $comment->user_id)
                                @if(auth()->id() === $comment->user_id && $editingCommentId !== $comment->id)
                                    <a href="{{ route('forum.posts.show', ['post' => $post, 'editing_comment' => $comment->id, 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage()]) }}#comment-{{ $comment->id }}" class="rounded-xl border border-[#D9C7B5] px-3 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                        Edit
                                    </a>
                                @endif
                                @include('forum.partials.inline-delete', [
                                    'id' => 'delete-comment-'.$comment->id,
                                    'action' => route('forum.comments.destroy', $comment),
                                    'message' => 'Delete this comment?',
                                    'buttonText' => 'Delete',
                                    'confirmText' => 'Delete Comment',
                                    'summaryClass' => 'rounded-xl border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 list-none cursor-pointer',
                                    'hiddenFields' => [
                                        'comments' => $commentSort,
                                        'comment_filter' => $commentFilter,
                                        'page' => $comments->currentPage(),
                                    ],
                                ])
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[2rem] border border-dashed border-[#D8C3A6] bg-white/70 px-6 py-10 text-center text-[#8B6B47]">
                    No comments yet. Start the discussion below.
                </div>
            @endforelse

            <div class="pt-2">
                {{ $comments->links() }}
            </div>
        </section>
    </div>
</div>

<div id="forum-social-menu" class="hidden fixed z-[95] min-w-[220px] rounded-2xl border border-[#D9C7B5] bg-white p-2 shadow-2xl shadow-[#2C1810]/20">
    <div class="px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Social Actions</div>
    <div id="forum-social-menu-user" class="px-3 pb-2 text-sm font-semibold text-[#4A2C2A]"></div>
    <button id="forum-social-friend-action" type="button" class="w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-[#4A2C2A] transition hover:bg-[#FBF7F1]"></button>
    <button id="forum-social-message-action" type="button" class="mt-1 w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-[#4A2C2A] transition hover:bg-[#FBF7F1]"></button>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const menu = document.getElementById('forum-social-menu');
    const userLabel = document.getElementById('forum-social-menu-user');
    const friendAction = document.getElementById('forum-social-friend-action');
    const messageAction = document.getElementById('forum-social-message-action');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const currentUserId = {{ (int) auth()->id() }};
    const friendRequestUrl = @json(route('friends.requests.store'));
    const messageStartUrl = @json(route('messages.start'));
    let activeTarget = null;
    let longPressTimer = null;

    function hideMenu() {
        menu.classList.add('hidden');
        activeTarget = null;
    }

    function submitHiddenForm(action, fields) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;
        form.classList.add('hidden');

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrfToken;
        form.appendChild(token);

        Object.entries(fields).forEach(([key, value]) => {
            if (value === null || value === undefined || value === '') {
                return;
            }

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = String(value);
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    function positionMenu(x, y) {
        const padding = 12;
        menu.classList.remove('hidden');
        const rect = menu.getBoundingClientRect();
        const maxLeft = window.innerWidth - rect.width - padding;
        const maxTop = window.innerHeight - rect.height - padding;
        menu.style.left = `${Math.max(padding, Math.min(x, maxLeft))}px`;
        menu.style.top = `${Math.max(padding, Math.min(y, maxTop))}px`;
    }

    function relationshipLabel(target) {
        const isFriend = target.dataset.isFriend === '1';
        const pendingSent = target.dataset.pendingSent === '1';
        const pendingReceived = target.dataset.pendingReceived === '1';

        if (isFriend) {
            return { text: 'Already friends', disabled: true };
        }

        if (pendingSent) {
            return { text: 'Friend request pending', disabled: true };
        }

        if (pendingReceived) {
            return { text: 'Accept friend request', disabled: false };
        }

        return { text: 'Send friend request', disabled: false };
    }

    function openMenu(target, x, y) {
        const targetUserId = Number(target.dataset.userId || 0);
        if (!targetUserId || targetUserId === currentUserId) {
            hideMenu();
            return;
        }

        activeTarget = target;
        userLabel.textContent = target.dataset.userName || 'Unknown user';

        const friendState = relationshipLabel(target);
        friendAction.textContent = friendState.text;
        friendAction.disabled = friendState.disabled;
        friendAction.className = `w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition ${
            friendState.disabled
                ? 'cursor-not-allowed text-[#A58A6A] bg-[#FBF7F1]'
                : 'text-[#4A2C2A] hover:bg-[#FBF7F1]'
        }`;

        const canMessage = target.dataset.isFriend === '1';
        messageAction.textContent = canMessage ? 'Send private message' : 'Message available after becoming friends';
        messageAction.disabled = !canMessage;
        messageAction.className = `mt-1 w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition ${
            canMessage
                ? 'text-[#4A2C2A] hover:bg-[#FBF7F1]'
                : 'cursor-not-allowed text-[#A58A6A] bg-[#FBF7F1]'
        }`;

        positionMenu(x, y);
    }

    function runPrimarySocialAction(target) {
        if (!target) {
            return;
        }

        const state = relationshipLabel(target);
        const isFriend = target.dataset.isFriend === '1';

        if (isFriend) {
            submitHiddenForm(messageStartUrl, {
                recipient_id: target.dataset.userId,
            });
            return;
        }

        if (state.disabled) {
            return;
        }

        submitHiddenForm(friendRequestUrl, {
            receiver_id: target.dataset.userId,
            source_type: target.dataset.sourceType,
            source_id: target.dataset.sourceId,
        });
    }

    document.querySelectorAll('.forum-social-target').forEach((target) => {
        target.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            openMenu(target, event.clientX, event.clientY);
        });

        target.addEventListener('pointerdown', (event) => {
            if (event.pointerType === 'mouse' && event.button !== 0) {
                return;
            }

            longPressTimer = window.setTimeout(() => {
                openMenu(target, event.clientX, event.clientY);
            }, 550);
        });

        ['pointerup', 'pointerleave', 'pointercancel', 'pointermove'].forEach((name) => {
            target.addEventListener(name, () => {
                if (longPressTimer) {
                    window.clearTimeout(longPressTimer);
                    longPressTimer = null;
                }
            });
        });
    });

    document.querySelectorAll('.forum-social-avatar').forEach((target) => {
        target.addEventListener('click', (event) => {
            event.preventDefault();

            if (target.dataset.socialMode === 'primary') {
                runPrimarySocialAction(target);
                return;
            }

            const rect = target.getBoundingClientRect();
            openMenu(target, rect.left + rect.width / 2, rect.bottom + 12);
        });
    });

    friendAction.addEventListener('click', () => {
        if (!activeTarget || friendAction.disabled) {
            return;
        }

        submitHiddenForm(friendRequestUrl, {
            receiver_id: activeTarget.dataset.userId,
            source_type: activeTarget.dataset.sourceType,
            source_id: activeTarget.dataset.sourceId,
        });
    });

    messageAction.addEventListener('click', () => {
        if (!activeTarget || messageAction.disabled) {
            return;
        }

        submitHiddenForm(messageStartUrl, {
            recipient_id: activeTarget.dataset.userId,
        });
    });

    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target)) {
            hideMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideMenu();
        }
    });

    window.addEventListener('scroll', hideMenu, { passive: true });
    window.addEventListener('resize', hideMenu);
})();
</script>
@endpush
