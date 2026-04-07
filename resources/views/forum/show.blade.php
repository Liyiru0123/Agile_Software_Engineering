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
                        <div class="mt-4 text-sm text-[#8B6B47]">
                            By {{ $post->user?->name ?? 'Unknown' }}
                            @if($post->user?->is_admin)
                                <span class="font-semibold text-[#6B3D2E]">&middot; Admin</span>
                            @endif
                            &middot; {{ optional($post->created_at)->format('Y-m-d H:i') }}
                            @if($post->updated_at && $post->updated_at->gt($post->created_at))
                                <span class="font-semibold text-[#6B3D2E]">&middot; Edited</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
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
                        <form method="POST" action="{{ route('forum.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post and all comments?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                Delete Post
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @unless($editingPost && auth()->id() === $post->user_id)
                <div class="forum-content-wrap mt-6 whitespace-pre-line rounded-[1.5rem] bg-[#FBF7F1] px-6 py-5 leading-8 text-[#3A2A22]">
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
                            <div class="text-sm text-[#8B6B47]">
                                {{ $comment->user?->name ?? 'Unknown' }}
                                @if($comment->user?->is_admin)
                                    <span class="font-semibold text-[#6B3D2E]">&middot; Admin</span>
                                @endif
                                &middot; {{ optional($comment->created_at)->format('Y-m-d H:i') }}
                                @if($comment->updated_at && $comment->updated_at->gt($comment->created_at))
                                    <span class="font-semibold text-[#6B3D2E]">&middot; Edited</span>
                                @endif
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
                                <div class="forum-content-wrap mt-3 whitespace-pre-line leading-7 text-[#3A2A22]">{{ $comment->body }}</div>
                                @include('forum.partials.attachment', ['item' => $comment])
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('forum.posts.show', ['post' => $post, 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage(), 'reply_to' => $comment->id]) }}#comment-form" class="rounded-xl border border-[#D9C7B5] px-3 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                Reply
                            </a>
                            @if(auth()->user()->is_admin || auth()->id() === $comment->user_id)
                                @if(auth()->id() === $comment->user_id && $editingCommentId !== $comment->id)
                                    <a href="{{ route('forum.posts.show', ['post' => $post, 'editing_comment' => $comment->id, 'comments' => $commentSort, 'comment_filter' => $commentFilter, 'page' => $comments->currentPage()]) }}#comment-{{ $comment->id }}" class="rounded-xl border border-[#D9C7B5] px-3 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                        Edit
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('forum.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="comments" value="{{ $commentSort }}">
                                    <input type="hidden" name="comment_filter" value="{{ $commentFilter }}">
                                    <input type="hidden" name="page" value="{{ $comments->currentPage() }}">
                                    <button type="submit" class="rounded-xl border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
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
@endsection
