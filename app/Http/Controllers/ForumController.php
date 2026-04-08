<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\ForumComment;
use App\Models\ForumCommentAttachment;
use App\Models\ForumNotification;
use App\Models\ForumPost;
use App\Models\ForumPostAttachment;
use App\Models\ForumTag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForumController extends Controller
{
    protected const POSTS_PER_PAGE = 10;
    protected const COMMENTS_PER_PAGE = 8;
    protected const PUBLIC_FORUM_NAME = 'Public Forum';
    protected const PUBLIC_FORUM_SLUG = 'public-forum';
    protected const PUBLIC_FORUM_DESCRIPTION = 'Open discussion for general learning reflections, questions, and study updates.';

    public function index(Request $request): View
    {
        $publicTag = $this->ensurePublicForumTag($request->user());
        $selectedTag = null;
        $selectedTags = collect();
        $selectedTagIds = collect();
        $scope = 'all';
        $search = trim($request->string('search')->toString());
        $sort = $request->string('sort')->toString();
        $author = $request->string('author')->toString();
        $timeframe = $this->normalizeTimeframe($request->string('timeframe')->toString());

        if (! in_array($sort, ['latest', 'oldest', 'most_commented', 'most_viewed', 'most_liked', 'trending'], true)) {
            $sort = 'latest';
        }

        if (! in_array($author, ['all', 'mine', 'participated'], true)) {
            $author = 'all';
        }

        $selectedTagIds = collect($request->input('tags', []))
            ->map(fn ($tagId) => (int) $tagId)
            ->filter(fn ($tagId) => $tagId > 0)
            ->values();

        if ($selectedTagIds->isEmpty() && $request->filled('tag')) {
            $selectedTag = ForumTag::query()
                ->where('slug', $request->string('tag'))
                ->firstOrFail();
            $selectedTagIds = collect([$selectedTag->id]);
        } elseif ($request->string('scope')->toString() === 'default') {
            $selectedTag = $publicTag;
            $selectedTagIds = collect([$publicTag->id]);
            $scope = 'tag';
        }

        $tags = $this->forumTagsQuery()
            ->withCount('posts')
            ->with('user:id,name')
            ->get();

        if ($selectedTagIds->isNotEmpty()) {
            $selectedTags = $tags
                ->whereIn('id', $selectedTagIds)
                ->values();

            if ($selectedTags->count() === 1) {
                $selectedTag = $selectedTags->first();
                $scope = 'tag';
            } else {
                $scope = 'tags';
            }
        }

        $posts = ForumPost::query()
            ->with(['user:id,name,is_admin', 'tag:id,name,slug,user_id'])
            ->withCount(['comments', 'likes', 'favorites', 'attachments'])
            ->when($timeframe !== 'all', fn ($query) => $this->applyTimeframe($query, $timeframe))
            ->when($selectedTagIds->isNotEmpty(), fn ($query) => $this->applyTagFilter($query, $selectedTagIds->all(), $publicTag->id))
            ->when($author === 'mine', fn ($query) => $query->where('user_id', $request->user()->id))
            ->when($author === 'participated', function ($query) use ($request) {
                $query->whereHas('comments', fn ($commentQuery) => $commentQuery->where('user_id', $request->user()->id));
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('title', 'like', '%'.$search.'%')
                        ->orWhere('body', 'like', '%'.$search.'%');
                });
            });

        $posts
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at');

        match ($sort) {
            'oldest' => $posts->oldest(),
            'most_commented' => $posts->orderByDesc('comments_count')->latest('id'),
            'most_viewed' => $posts->orderByDesc('view_count')->latest('id'),
            'most_liked' => $posts->orderByDesc('likes_count')->orderByDesc('comments_count')->orderByDesc('view_count')->latest('id'),
            'trending' => $posts->orderByDesc('comments_count')->orderByDesc('likes_count')->orderByDesc('view_count')->latest('id'),
            default => $posts->latest(),
        };

        $posts = $posts
            ->paginate(self::POSTS_PER_PAGE)
            ->withQueryString();

        $this->attachReactionStateToPosts($posts->getCollection(), $request->user());
        $this->decoratePostsForDisplay($posts->getCollection(), $search);

        $topLikedPosts = ForumPost::query()
            ->with(['user:id,name', 'tag:id,name,slug,user_id'])
            ->withCount(['likes', 'comments', 'favorites', 'attachments'])
            ->when($timeframe !== 'all', fn ($query) => $this->applyTimeframe($query, $timeframe))
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->orderByDesc('view_count')
            ->take(5)
            ->get();

        $trendingPosts = ForumPost::query()
            ->with(['user:id,name', 'tag:id,name,slug,user_id'])
            ->withCount(['likes', 'comments', 'favorites', 'attachments'])
            ->when($timeframe !== 'all', fn ($query) => $this->applyTimeframe($query, $timeframe))
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('comments_count')
            ->orderByDesc('likes_count')
            ->orderByDesc('view_count')
            ->latest('id')
            ->take(5)
            ->get();

        $this->attachReactionStateToPosts($topLikedPosts, $request->user());
        $this->attachReactionStateToPosts($trendingPosts, $request->user());

        return view('forum.index', [
            'tags' => $tags,
            'posts' => $posts,
            'topLikedPosts' => $topLikedPosts,
            'trendingPosts' => $trendingPosts,
            'publicTag' => $publicTag,
            'selectedTag' => $selectedTag,
            'selectedTags' => $selectedTags,
            'selectedTagIds' => $selectedTagIds->all(),
            'selectedScope' => $scope,
            'search' => $search,
            'sort' => $sort,
            'author' => $author,
            'timeframe' => $timeframe,
        ]);
    }

    public function show(Request $request, ForumPost $post): View
    {
        $post->increment('view_count');

        $this->markRelevantForumNotificationsAsRead($request, $post);

        $publicTag = $this->ensurePublicForumTag($request->user());
        $tags = $this->forumTagsQuery()->get(['id', 'name', 'slug']);
        $commentSort = $request->string('comments')->toString() === 'latest' ? 'latest' : 'oldest';
        $commentFilter = $this->normalizeCommentFilter($request->string('comment_filter')->toString());
        $replyToComment = null;

        if ($request->filled('reply_to')) {
            $replyToComment = ForumComment::query()
                ->with('user:id,name')
                ->where('forum_post_id', $post->id)
                ->find($request->integer('reply_to'));
        }

        $post = $post->fresh()
            ->load([
                'user:id,name,is_admin',
                'tag:id,name,slug,user_id',
                'attachments',
            ])
            ->loadCount(['comments', 'likes', 'favorites', 'attachments']);

        $comments = ForumComment::query()
            ->with(['user:id,name,is_admin', 'replyParent.user:id,name', 'attachments'])
            ->where('forum_post_id', $post->id)
            ->when($commentFilter === 'author', fn ($query) => $query->where('user_id', $post->user_id))
            ->when($commentFilter === 'mine', fn ($query) => $query->where('user_id', $request->user()->id))
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->when($commentSort === 'latest', fn ($query) => $query->latest(), fn ($query) => $query->oldest())
            ->paginate(self::COMMENTS_PER_PAGE)
            ->withQueryString();

        $this->attachReactionStateToPost($post, $request->user());
        $post->can_pin = $this->canPinPost($request->user(), $post);
        $comments->getCollection()->transform(function (ForumComment $comment) use ($request, $post) {
            $comment->can_pin = $this->canPinComment($request->user(), $post);

            return $comment;
        });

        $socialTargetStates = $this->buildSocialTargetStates(
            $request->user()->id,
            collect([$post->user_id])->merge($comments->getCollection()->pluck('user_id'))
        );

        return view('forum.show', compact('post', 'tags', 'publicTag', 'commentSort', 'commentFilter', 'replyToComment', 'comments', 'socialTargetStates'));
    }

    public function saved(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $sort = $request->string('sort')->toString();
        $selectedTagIds = collect($request->input('tags', []))
            ->map(fn ($tagId) => (int) $tagId)
            ->filter(fn ($tagId) => $tagId > 0)
            ->values();

        if (! in_array($sort, ['saved_latest', 'saved_oldest', 'most_liked', 'most_viewed'], true)) {
            $sort = 'saved_latest';
        }

        $this->ensurePublicForumTag($request->user());

        $tags = $this->forumTagsQuery()
            ->withCount('posts')
            ->get(['id', 'name', 'slug']);

        $posts = $request->user()
            ->favoritedForumPosts()
            ->with(['user:id,name,is_admin', 'tag:id,name,slug,user_id'])
            ->withCount(['comments', 'likes', 'favorites', 'attachments'])
            ->when($selectedTagIds->isNotEmpty(), fn ($query) => $query->whereIn('forum_tag_id', $selectedTagIds->all()))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('title', 'like', '%'.$search.'%')
                        ->orWhere('body', 'like', '%'.$search.'%');
                });
            });

        $posts
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at');

        match ($sort) {
            'saved_oldest' => $posts->orderBy('forum_post_favorites.created_at'),
            'most_liked' => $posts->orderByDesc('likes_count')->orderByDesc('comments_count')->orderByDesc('view_count'),
            'most_viewed' => $posts->orderByDesc('view_count')->orderByDesc('likes_count')->orderByDesc('comments_count'),
            default => $posts->orderByDesc('forum_post_favorites.created_at'),
        };

        $posts = $posts
            ->paginate(self::POSTS_PER_PAGE)
            ->withQueryString();

        $this->attachReactionStateToPosts($posts->getCollection(), $request->user());
        $this->decoratePostsForDisplay($posts->getCollection(), $search);

        return view('forum.saved', [
            'posts' => $posts,
            'tags' => $tags,
            'selectedTagIds' => $selectedTagIds->all(),
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    public function myForum(Request $request): View
    {
        $user = $request->user();
        $unreadPostCounts = ForumNotification::query()
            ->where('user_id', $user->id)
            ->where('type', 'post_commented')
            ->whereNull('read_at')
            ->selectRaw('forum_post_id, COUNT(*) as unread_count')
            ->groupBy('forum_post_id')
            ->pluck('unread_count', 'forum_post_id');

        $unreadCommentCounts = ForumNotification::query()
            ->where('user_id', $user->id)
            ->where('type', 'comment_replied')
            ->whereNull('read_at')
            ->whereNotNull('target_forum_comment_id')
            ->selectRaw('target_forum_comment_id, COUNT(*) as unread_count')
            ->groupBy('target_forum_comment_id')
            ->pluck('unread_count', 'target_forum_comment_id');

        $forumPostsSummary = [
            'count' => ForumPost::query()
                ->where('user_id', $user->id)
                ->count(),
            'recent' => ForumPost::query()
                ->with('tag:id,name,slug')
                ->where('user_id', $user->id)
                ->latest()
                ->take(6)
                ->get()
                ->map(fn (ForumPost $post) => [
                    'title' => $post->title,
                    'tag_name' => $post->tag?->name,
                    'url' => route('forum.posts.show', ['post' => $post, 'notification_post' => 1]),
                    'created_at' => optional($post->created_at)?->diffForHumans(),
                    'unread_count' => (int) ($unreadPostCounts[$post->id] ?? 0),
                ]),
        ];

        $forumCommentsSummary = [
            'count' => ForumComment::query()
                ->where('user_id', $user->id)
                ->count(),
            'recent' => ForumComment::query()
                ->with('post:id,title')
                ->where('user_id', $user->id)
                ->latest()
                ->take(6)
                ->get()
                ->map(fn (ForumComment $comment) => [
                    'excerpt' => Str::limit($comment->body, 90),
                    'post_title' => $comment->post?->title ?? 'Forum Post',
                    'url' => $comment->post
                        ? route('forum.posts.show', ['post' => $comment->post, 'notification_comment' => $comment->id]).'#comment-'.$comment->id
                        : route('forum.index'),
                    'created_at' => optional($comment->created_at)?->diffForHumans(),
                    'unread_count' => (int) ($unreadCommentCounts[$comment->id] ?? 0),
                ]),
        ];

        $forumSavedSummary = [
            'count' => $user->favoritedForumPosts()->count(),
            'recent' => $user->favoritedForumPosts()
                ->with(['tag:id,name,slug', 'user:id,name'])
                ->latest('forum_post_favorites.created_at')
                ->take(6)
                ->get()
                ->map(fn (ForumPost $post) => [
                    'title' => $post->title,
                    'tag_name' => $post->tag?->name,
                    'author_name' => $post->user?->name ?? 'Unknown',
                    'url' => route('forum.posts.show', $post),
                    'saved_at' => optional($post->pivot?->created_at)?->diffForHumans(),
                ]),
        ];

        return view('forum.my', [
            'forumPostsSummary' => $forumPostsSummary,
            'forumCommentsSummary' => $forumCommentsSummary,
            'forumSavedSummary' => $forumSavedSummary,
        ]);
    }

    public function createTag(): View
    {
        return view('forum.create-tag');
    }

    public function createPost(Request $request): View
    {
        $publicTag = $this->ensurePublicForumTag($request->user());
        $selectedTag = $publicTag;

        if ($request->filled('tag')) {
            $selectedTag = ForumTag::query()
                ->where('slug', $request->string('tag'))
                ->first() ?? $publicTag;
        }

        $tags = $this->forumTagsQuery()
            ->get(['id', 'name', 'slug']);

        return view('forum.create-post', [
            'tags' => $tags,
            'selectedTag' => $selectedTag,
            'publicTag' => $publicTag,
        ]);
    }

    public function storeTag(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:forum_tags,name'],
            'description' => ['nullable', 'string', 'max:240'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug !== '' ? $baseSlug : 'forum-tag';
        $counter = 2;

        while (ForumTag::query()->where('slug', $slug)->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'forum-tag').'-'.$counter;
            $counter++;
        }

        $tag = ForumTag::query()->create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('forum.index', ['tag' => $tag->slug])
            ->with('forum_status', 'Tag created successfully.');
    }

    public function destroyTag(Request $request, ForumTag $tag): RedirectResponse
    {
        abort_unless($this->canManageTag($request->user(), $tag), 403);

        if ($this->isPublicForumTag($tag)) {
            return redirect()
                ->route('forum.index', ['tag' => self::PUBLIC_FORUM_SLUG])
                ->with('forum_status', 'Public Forum is the system default tag and cannot be deleted.');
        }

        $publicTag = $this->ensurePublicForumTag($request->user());
        ForumPost::query()
            ->where('forum_tag_id', $tag->id)
            ->update(['forum_tag_id' => $publicTag->id]);

        $tag->delete();

        return redirect()
            ->route('forum.index', ['tag' => $publicTag->slug])
            ->with('forum_status', 'Tag deleted. Related posts were moved to Public Forum.');
    }

    public function storePost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'min:12', 'max:5000'],
            'forum_tag_id' => ['nullable', 'integer', 'exists:forum_tags,id'],
            'attachments' => $this->attachmentCollectionValidationRules(),
            'attachments.*' => $this->attachmentValidationRules(),
        ]);

        $postData = [
            'user_id' => $request->user()->id,
            'forum_tag_id' => $this->resolveForumTagId($validated['forum_tag_id'] ?? null, $request->user()),
            'title' => $validated['title'],
            'body' => trim($validated['body']),
        ];

        $post = ForumPost::query()->create($postData);
        $this->storePostAttachments($post, $request->file('attachments', []));

        return redirect()
            ->route('forum.posts.show', $post)
            ->with('forum_status', 'Post published successfully.');
    }

    public function updatePost(Request $request, ForumPost $post): RedirectResponse
    {
        abort_unless($this->canEditOwnContent($request->user(), $post->user_id), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'min:12', 'max:5000'],
            'forum_tag_id' => ['nullable', 'integer', 'exists:forum_tags,id'],
            'attachments' => $this->attachmentCollectionValidationRules(),
            'attachments.*' => $this->attachmentValidationRules(),
            'remove_attachments' => ['nullable', 'boolean'],
        ]);

        $postData = [
            'forum_tag_id' => $this->resolveForumTagId($validated['forum_tag_id'] ?? null, $request->user()),
            'title' => trim($validated['title']),
            'body' => trim($validated['body']),
        ];

        $post->update($postData);
        $this->syncPostAttachments(
            $post,
            $request->file('attachments', []),
            (bool) ($validated['remove_attachments'] ?? false)
        );

        return redirect()
            ->route('forum.posts.show', $post)
            ->with('forum_status', 'Post updated successfully.');
    }

    public function destroyPost(Request $request, ForumPost $post): RedirectResponse
    {
        abort_unless($this->canModerateContent($request->user(), $post->user_id), 403);

        $post->delete();

        return redirect()
            ->route('forum.index')
            ->with('forum_status', 'Post deleted successfully.');
    }

    public function storeComment(Request $request, ForumPost $post): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
            'reply_to_comment_id' => ['nullable', 'integer', 'exists:forum_comments,id'],
            'attachments' => $this->attachmentCollectionValidationRules(),
            'attachments.*' => $this->attachmentValidationRules(),
        ]);

        $replyToCommentId = $this->resolveReplyCommentId($validated['reply_to_comment_id'] ?? null, $post->id);

        $commentData = [
            'user_id' => $request->user()->id,
            'forum_post_id' => $post->id,
            'reply_to_comment_id' => $replyToCommentId,
            'body' => trim($validated['body']),
        ];

        $comment = ForumComment::query()->create($commentData);
        $this->storeCommentAttachments($comment, $request->file('attachments', []));
        $this->createNotificationsForComment($comment);

        $commentSort = $request->string('comments')->toString() === 'latest' ? 'latest' : 'oldest';
        $commentFilter = $this->normalizeCommentFilter($request->string('comment_filter')->toString());
        $targetPage = $commentSort === 'latest'
            ? 1
            : (int) ceil($post->comments()->count() / self::COMMENTS_PER_PAGE);

        return redirect()
            ->to(route('forum.posts.show', $this->forumShowParameters($post, $commentSort, max($targetPage, 1), $commentFilter)).'#comment-'.$comment->id)
            ->with('forum_status', 'Comment posted successfully.');
    }

    public function updateComment(Request $request, ForumComment $comment): RedirectResponse
    {
        abort_unless($this->canEditOwnContent($request->user(), $comment->user_id), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
            'attachments' => $this->attachmentCollectionValidationRules(),
            'attachments.*' => $this->attachmentValidationRules(),
            'remove_attachments' => ['nullable', 'boolean'],
        ]);

        $commentData = [
            'body' => trim($validated['body']),
        ];

        $comment->update($commentData);
        $this->syncCommentAttachments(
            $comment,
            $request->file('attachments', []),
            (bool) ($validated['remove_attachments'] ?? false)
        );

        $commentSort = $request->string('comments')->toString() === 'latest' ? 'latest' : 'oldest';
        $commentFilter = $this->normalizeCommentFilter($request->string('comment_filter')->toString());
        $page = max(1, $request->integer('page', 1));

        return redirect()
            ->to(route('forum.posts.show', $this->forumShowParameters($comment->post, $commentSort, $page, $commentFilter)).'#comment-'.$comment->id)
            ->with('forum_status', 'Comment updated successfully.');
    }

    public function destroyComment(Request $request, ForumComment $comment): RedirectResponse
    {
        abort_unless($this->canModerateContent($request->user(), $comment->user_id), 403);

        $post = $comment->post;
        $comment->delete();

        $commentSort = $request->string('comments')->toString() === 'latest' ? 'latest' : 'oldest';
        $commentFilter = $this->normalizeCommentFilter($request->string('comment_filter')->toString());
        $remainingCount = $post->comments()->count();
        $maxPage = max(1, (int) ceil(max($remainingCount, 1) / self::COMMENTS_PER_PAGE));
        $page = min(max(1, $request->integer('page', 1)), $maxPage);

        return redirect()
            ->route('forum.posts.show', $this->forumShowParameters($post, $commentSort, $page, $commentFilter))
            ->with('forum_status', 'Comment deleted successfully.');
    }

    public function toggleLike(Request $request, ForumPost $post): RedirectResponse
    {
        $user = $request->user();
        $alreadyLiked = $post->likes()->where('user_id', $user->id)->exists();

        if ($alreadyLiked) {
            $post->likes()->detach($user->id);
            $message = 'Like removed.';
        } else {
            $post->likes()->attach($user->id);
            $message = 'Post liked.';
        }

        return back()->with('forum_status', $message);
    }

    public function toggleFavorite(Request $request, ForumPost $post): RedirectResponse
    {
        $user = $request->user();
        $alreadyFavorited = $post->favorites()->where('user_id', $user->id)->exists();

        if ($alreadyFavorited) {
            $post->favorites()->detach($user->id);
            $message = 'Removed from saved posts.';
        } else {
            $post->favorites()->attach($user->id);
            $message = 'Post saved to your favorites.';
        }

        return back()->with('forum_status', $message);
    }

    public function togglePostPin(Request $request, ForumPost $post): RedirectResponse
    {
        abort_unless($this->canPinPost($request->user(), $post), 403);

        $post->update([
            'is_pinned' => ! $post->is_pinned,
            'pinned_at' => $post->is_pinned ? null : now(),
        ]);

        return back()->with('forum_status', $post->fresh()->is_pinned ? 'Post pinned successfully.' : 'Post unpinned successfully.');
    }

    public function toggleCommentPin(Request $request, ForumComment $comment): RedirectResponse
    {
        $post = $comment->post()->with('tag')->firstOrFail();
        abort_unless($this->canPinComment($request->user(), $post), 403);

        $comment->update([
            'is_pinned' => ! $comment->is_pinned,
            'pinned_at' => $comment->is_pinned ? null : now(),
        ]);

        $commentSort = $request->string('comments')->toString() === 'latest' ? 'latest' : 'oldest';
        $commentFilter = $this->normalizeCommentFilter($request->string('comment_filter')->toString());
        $page = max(1, $request->integer('page', 1));

        return redirect()
            ->to(route('forum.posts.show', $this->forumShowParameters($post, $commentSort, $page, $commentFilter)).'#comment-'.$comment->id)
            ->with('forum_status', $comment->fresh()->is_pinned ? 'Comment pinned successfully.' : 'Comment unpinned successfully.');
    }

    protected function canModerateContent($user, int $ownerId): bool
    {
        return (bool) ($user?->is_admin || $user?->id === $ownerId);
    }

    protected function canEditOwnContent($user, int $ownerId): bool
    {
        return (bool) ($user?->id === $ownerId);
    }

    protected function canManageTag($user, ForumTag $tag): bool
    {
        return (bool) ($user?->is_admin || $user?->id === $tag->user_id);
    }

    protected function canPinPost($user, ForumPost $post): bool
    {
        return (bool) (
            $user?->is_admin
            || $user?->id === $post->tag?->user_id
        );
    }

    protected function canPinComment($user, ForumPost $post): bool
    {
        return (bool) (
            $user?->is_admin
            || $user?->id === $post->user_id
        );
    }

    protected function excerptWithHighlight(string $text, string $search, int $limit = 220): string
    {
        $text = $this->normalizeForumDisplayText($text);

        if ($search === '') {
            return e(Str::limit($text, $limit));
        }

        $position = mb_stripos($text, $search);

        if ($position === false) {
            return $this->highlightText(Str::limit($text, $limit), $search);
        }

        $start = max(0, $position - (int) floor($limit / 3));
        $excerpt = mb_substr($text, $start, $limit);

        if ($start > 0) {
            $excerpt = '...'.$excerpt;
        }

        if (($start + $limit) < mb_strlen($text)) {
            $excerpt .= '...';
        }

        return $this->highlightText($excerpt, $search);
    }

    protected function highlightText(string $text, string $search): string
    {
        $text = $this->normalizeForumDisplayText($text);

        if ($search === '') {
            return e($text);
        }

        $segments = preg_split('/('.preg_quote($search, '/').')/iu', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($segments === false) {
            return e($text);
        }

        return collect($segments)
            ->map(function (string $segment) use ($search) {
                if ($segment !== '' && mb_strtolower($segment) === mb_strtolower($search)) {
                    return '<mark class="rounded bg-[#F3E7D8] px-1 text-[#6B3D2E]">'.e($segment).'</mark>';
                }

                return e($segment);
            })
            ->implode('');
    }

    protected function normalizeForumDisplayText(string $text): string
    {
        $text = preg_replace('/\[\[forum-image:\d+\]\]/', '', $text) ?? $text;
        $text = preg_replace('/^##\s+/m', '', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    protected function attachReactionStateToPosts($posts, $user): void
    {
        if (! $user || $posts->isEmpty()) {
            $posts->each(function (ForumPost $post) {
                $post->liked_by_user = false;
                $post->favorited_by_user = false;
                $post->can_pin = false;
            });

            return;
        }

        $postIds = $posts->pluck('id');
        $likedIds = $user->likedForumPosts()
            ->whereIn('forum_posts.id', $postIds)
            ->pluck('forum_posts.id')
            ->all();
        $favoritedIds = $user->favoritedForumPosts()
            ->whereIn('forum_posts.id', $postIds)
            ->pluck('forum_posts.id')
            ->all();

        $posts->each(function (ForumPost $post) use ($likedIds, $favoritedIds, $user) {
            $post->liked_by_user = in_array($post->id, $likedIds, true);
            $post->favorited_by_user = in_array($post->id, $favoritedIds, true);
            $post->can_pin = $this->canPinPost($user, $post);
        });
    }

    protected function attachReactionStateToPost(ForumPost $post, $user): void
    {
        if (! $user) {
            $post->liked_by_user = false;
            $post->favorited_by_user = false;

            return;
        }

        $post->liked_by_user = $post->likes()->where('user_id', $user->id)->exists();
        $post->favorited_by_user = $post->favorites()->where('user_id', $user->id)->exists();
        $post->can_pin = $this->canPinPost($user, $post);
    }

    protected function resolveReplyCommentId(?int $replyToCommentId, int $postId): ?int
    {
        if (! $replyToCommentId) {
            return null;
        }

        $replyToComment = ForumComment::query()
            ->where('forum_post_id', $postId)
            ->find($replyToCommentId);

        return $replyToComment?->id;
    }

    protected function createNotificationsForComment(ForumComment $comment): void
    {
        $comment->loadMissing([
            'post:id,user_id,title',
            'replyParent:id,user_id',
        ]);

        $recipientTypes = [];
        $actorId = (int) $comment->user_id;
        $postOwnerId = (int) ($comment->post?->user_id ?? 0);
        $replyOwnerId = (int) ($comment->replyParent?->user_id ?? 0);
        $replyParentId = (int) ($comment->replyParent?->id ?? 0);

        if ($postOwnerId > 0 && $postOwnerId !== $actorId) {
            $recipientTypes[$postOwnerId] = 'post_commented';
        }

        if ($replyOwnerId > 0 && $replyOwnerId !== $actorId) {
            $recipientTypes[$replyOwnerId] = 'comment_replied';
        }

        foreach ($recipientTypes as $recipientId => $type) {
            ForumNotification::query()->create([
                'user_id' => $recipientId,
                'actor_id' => $actorId,
                'forum_post_id' => $comment->forum_post_id,
                'forum_comment_id' => $comment->id,
                'target_forum_comment_id' => $type === 'comment_replied' ? $replyParentId : null,
                'type' => $type,
            ]);
        }
    }

    protected function markRelevantForumNotificationsAsRead(Request $request, ForumPost $post): void
    {
        $user = $request->user();

        if (! $user) {
            return;
        }

        if ($request->boolean('notification_post') && (int) $post->user_id === (int) $user->id) {
            ForumNotification::query()
                ->where('user_id', $user->id)
                ->where('forum_post_id', $post->id)
                ->where('type', 'post_commented')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $targetCommentId = $request->integer('notification_comment');

        if ($targetCommentId < 1) {
            return;
        }

        $ownsComment = ForumComment::query()
            ->where('id', $targetCommentId)
            ->where('forum_post_id', $post->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $ownsComment) {
            return;
        }

        ForumNotification::query()
            ->where('user_id', $user->id)
            ->where('type', 'comment_replied')
            ->where('target_forum_comment_id', $targetCommentId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    protected function decoratePostsForDisplay($posts, string $search): void
    {
        $posts->transform(function (ForumPost $post) use ($search) {
            $post->highlighted_title = $this->highlightText($post->title, $search);
            $post->highlighted_excerpt = $this->excerptWithHighlight($post->body, $search);

            return $post;
        });
    }

    protected function forumShowParameters(ForumPost $post, string $commentSort, int $page = 1, string $commentFilter = 'all'): array
    {
        $parameters = ['post' => $post];

        if ($commentSort !== 'oldest') {
            $parameters['comments'] = $commentSort;
        }

        if ($commentFilter !== 'all') {
            $parameters['comment_filter'] = $commentFilter;
        }

        if ($page > 1) {
            $parameters['page'] = $page;
        }

        return $parameters;
    }

    protected function normalizeTimeframe(string $timeframe): string
    {
        return in_array($timeframe, ['all', '7d', '30d'], true) ? $timeframe : 'all';
    }

    protected function normalizeCommentFilter(string $commentFilter): string
    {
        return in_array($commentFilter, ['all', 'author', 'mine'], true) ? $commentFilter : 'all';
    }

    protected function buildSocialTargetStates(int $currentUserId, $targetUserIds): array
    {
        $ids = collect($targetUserIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->reject(fn (int $id) => $id === $currentUserId)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $friendIds = Friendship::query()
            ->where(function ($query) use ($currentUserId, $ids) {
                $query->where('user_one_id', $currentUserId)
                    ->whereIn('user_two_id', $ids);
            })
            ->orWhere(function ($query) use ($currentUserId, $ids) {
                $query->where('user_two_id', $currentUserId)
                    ->whereIn('user_one_id', $ids);
            })
            ->get()
            ->map(fn (Friendship $friendship) => $friendship->user_one_id === $currentUserId ? $friendship->user_two_id : $friendship->user_one_id)
            ->unique()
            ->values();

        $sentPendingIds = FriendRequest::query()
            ->where('sender_id', $currentUserId)
            ->where('status', 'pending')
            ->whereIn('receiver_id', $ids)
            ->pluck('receiver_id');

        $receivedPendingIds = FriendRequest::query()
            ->where('receiver_id', $currentUserId)
            ->where('status', 'pending')
            ->whereIn('sender_id', $ids)
            ->pluck('sender_id');

        return $ids
            ->mapWithKeys(fn (int $id) => [
                $id => [
                    'is_friend' => $friendIds->contains($id),
                    'pending_sent' => $sentPendingIds->contains($id),
                    'pending_received' => $receivedPendingIds->contains($id),
                ],
            ])
            ->all();
    }

    protected function applyTimeframe($query, string $timeframe)
    {
        return match ($timeframe) {
            '7d' => $query->where('created_at', '>=', now()->subDays(7)),
            '30d' => $query->where('created_at', '>=', now()->subDays(30)),
            default => $query,
        };
    }

    protected function forumTagsQuery()
    {
        return ForumTag::query()
            ->orderByRaw("case when slug = ? then 0 else 1 end", [self::PUBLIC_FORUM_SLUG])
            ->orderBy('name');
    }

    protected function ensurePublicForumTag(?User $user): ForumTag
    {
        $owner = User::query()
            ->where('is_admin', true)
            ->oldest('id')
            ->first()
            ?? $user
            ?? User::query()->oldest('id')->first();

        if (! $owner) {
            abort(500, 'A forum user is required before Public Forum can be created.');
        }

        $tag = ForumTag::query()
            ->where('slug', self::PUBLIC_FORUM_SLUG)
            ->orWhere('name', self::PUBLIC_FORUM_NAME)
            ->first();

        if ($tag) {
            $tag->update([
                'user_id' => $tag->user_id ?: $owner->id,
                'name' => self::PUBLIC_FORUM_NAME,
                'slug' => self::PUBLIC_FORUM_SLUG,
                'description' => $tag->description ?: self::PUBLIC_FORUM_DESCRIPTION,
            ]);

            return $tag->fresh();
        }

        return ForumTag::query()->create([
            'user_id' => $owner->id,
            'name' => self::PUBLIC_FORUM_NAME,
            'slug' => self::PUBLIC_FORUM_SLUG,
            'description' => self::PUBLIC_FORUM_DESCRIPTION,
        ]);
    }

    protected function resolveForumTagId(?int $forumTagId, ?User $user): int
    {
        if ($forumTagId) {
            return $forumTagId;
        }

        return $this->ensurePublicForumTag($user)->id;
    }

    protected function applyTagFilter($query, array $selectedTagIds, int $publicTagId)
    {
        $selectedTagIds = array_values(array_unique(array_map('intval', $selectedTagIds)));

        if (in_array($publicTagId, $selectedTagIds, true)) {
            return $query->where(function ($nestedQuery) use ($selectedTagIds) {
                $nestedQuery
                    ->whereIn('forum_tag_id', $selectedTagIds)
                    ->orWhereNull('forum_tag_id');
            });
        }

        return $query->whereIn('forum_tag_id', $selectedTagIds);
    }

    protected function isPublicForumTag(ForumTag $tag): bool
    {
        return $tag->slug === self::PUBLIC_FORUM_SLUG;
    }

    protected function attachmentValidationRules(): array
    {
        return [
            'file',
            'max:5120',
            'mimes:jpg,jpeg,png,gif,webp',
        ];
    }

    protected function attachmentCollectionValidationRules(): array
    {
        return ['nullable', 'array'];
    }

    protected function storeAttachmentPayload(UploadedFile $file, string $directory, int $sortOrder): array
    {
        return [
            'path' => $file->store($directory, 'public'),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'sort_order' => $sortOrder,
        ];
    }

    protected function storePostAttachments(ForumPost $post, array $files): void
    {
        if ($files === []) {
            return;
        }

        $nextSortOrder = (int) $post->attachments()->max('sort_order') + 1;

        foreach ($files as $index => $file) {
            $post->attachments()->create(
                $this->storeAttachmentPayload($file, 'forum/posts', $nextSortOrder + $index)
            );
        }
    }

    protected function storeCommentAttachments(ForumComment $comment, array $files): void
    {
        if ($files === []) {
            return;
        }

        $nextSortOrder = (int) $comment->attachments()->max('sort_order') + 1;

        foreach ($files as $index => $file) {
            $comment->attachments()->create(
                $this->storeAttachmentPayload($file, 'forum/comments', $nextSortOrder + $index)
            );
        }
    }

    protected function syncPostAttachments(ForumPost $post, array $files, bool $removeAttachments): void
    {
        if ($removeAttachments) {
            $post->attachments()->get()->each(fn (ForumPostAttachment $attachment) => $attachment->delete());
            $this->clearLegacyAttachment($post);
        }

        $this->storePostAttachments($post, $files);
    }

    protected function syncCommentAttachments(ForumComment $comment, array $files, bool $removeAttachments): void
    {
        if ($removeAttachments) {
            $comment->attachments()->get()->each(fn (ForumCommentAttachment $attachment) => $attachment->delete());
            $this->clearLegacyAttachment($comment);
        }

        $this->storeCommentAttachments($comment, $files);
    }

    protected function clearLegacyAttachment($resource): void
    {
        if (! $resource->hasLegacyAttachment()) {
            return;
        }

        $resource->deleteAttachmentFile();
        $resource->forceFill([
            'attachment_path' => null,
            'attachment_original_name' => null,
            'attachment_mime_type' => null,
            'attachment_size' => null,
        ])->save();
    }
}
