<?php

namespace Tests\Feature;

use App\Models\ForumComment;
use App\Models\ForumNotification;
use App\Models\ForumPost;
use App\Models\ForumTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ForumFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_tags_posts_and_comments_and_see_them_on_my_forum_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('forum.tags.store'), [
            'name' => 'Writing Tips',
            'description' => 'Share writing ideas.',
        ])->assertRedirect();

        $tag = ForumTag::query()->first();
        $this->assertNotNull($tag);

        $this->actingAs($user)->post(route('forum.posts.store'), [
            'title' => 'How I practice paraphrasing',
            'body' => 'I usually compare the original sentence with my own version and then revise the structure.',
            'forum_tag_id' => $tag->id,
        ])->assertRedirect();

        $post = ForumPost::query()->first();
        $this->assertNotNull($post);

        $this->actingAs($user)->post(route('forum.comments.store', $post), [
            'body' => 'This method also helps me notice repeated vocabulary.',
        ])->assertRedirect();

        $this->get(route('forum.posts.show', $post))
            ->assertOk()
            ->assertSee('How I practice paraphrasing')
            ->assertSee('This method also helps me notice repeated vocabulary.');

        $this->actingAs($user)->get(route('forum.my'))
            ->assertOk()
            ->assertSee('My Forum')
            ->assertSee('How I practice paraphrasing')
            ->assertSee('Writing Tips');
    }

    public function test_user_can_delete_own_post_and_comment(): void
    {
        $user = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'My Study Reflection',
            'body' => 'I learned more when I reviewed my mistakes carefully after each task.',
        ]);

        $comment = ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
            'body' => 'I want to add one more example here.',
        ]);

        $this->actingAs($user)->delete(route('forum.comments.destroy', $comment))
            ->assertRedirect(route('forum.posts.show', $post));

        $this->assertDatabaseMissing('forum_comments', ['id' => $comment->id]);

        $this->actingAs($user)->delete(route('forum.posts.destroy', $post))
            ->assertRedirect(route('forum.index'));

        $this->assertDatabaseMissing('forum_posts', ['id' => $post->id]);
    }

    public function test_admin_can_delete_any_tag_post_and_comment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create();
        $tag = ForumTag::query()->create([
            'user_id' => $author->id,
            'name' => 'Reading Club',
            'slug' => 'reading-club',
        ]);

        $post = ForumPost::query()->create([
            'user_id' => $author->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Reading strategy notes',
            'body' => 'I annotate topic sentences first and then locate details.',
        ]);

        $comment = ForumComment::query()->create([
            'user_id' => $author->id,
            'forum_post_id' => $post->id,
            'body' => 'This strategy saves time in long passages.',
        ]);

        $this->actingAs($admin)->delete(route('forum.comments.destroy', $comment))
            ->assertRedirect(route('forum.posts.show', $post));

        $this->assertDatabaseMissing('forum_comments', ['id' => $comment->id]);

        $this->actingAs($admin)->delete(route('forum.posts.destroy', $post))
            ->assertRedirect(route('forum.index'));

        $this->assertDatabaseMissing('forum_posts', ['id' => $post->id]);

        $this->actingAs($admin)->delete(route('forum.tags.destroy', $tag))
            ->assertRedirect(route('forum.index', ['tag' => 'public-forum']));

        $this->assertDatabaseMissing('forum_tags', ['id' => $tag->id]);
    }

    public function test_forum_search_and_tag_filter_can_work_together(): void
    {
        $user = User::factory()->create();
        $tag = ForumTag::query()->create([
            'user_id' => $user->id,
            'name' => 'Writing',
            'slug' => 'writing',
        ]);

        $matchingPost = ForumPost::query()->create([
            'user_id' => $user->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Paraphrase checklist',
            'body' => 'I rewrite the sentence, then compare grammar and vocabulary choices.',
        ]);

        ForumPost::query()->create([
            'user_id' => $user->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Speaking notes',
            'body' => 'I focus on fluency and sentence rhythm when shadowing audio.',
        ]);

        ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Default forum note',
            'body' => 'This post should not appear inside the writing tag scope.',
        ]);

        $this->actingAs($user)
            ->get(route('forum.index', ['tag' => $tag->slug, 'search' => 'checklist']))
            ->assertOk()
            ->assertSeeText($matchingPost->title)
            ->assertDontSeeText('Speaking notes')
            ->assertDontSeeText('Default forum note');
    }

    public function test_forum_posts_can_be_sorted_by_comment_count(): void
    {
        $user = User::factory()->create();

        $lessDiscussed = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Less discussed',
            'body' => 'A short discussion about learning vocabulary in context.',
        ]);

        $mostDiscussed = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Most discussed',
            'body' => 'A discussion that should appear first when sorting by comments.',
        ]);

        ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $lessDiscussed->id,
            'body' => 'One comment here.',
        ]);

        ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $mostDiscussed->id,
            'body' => 'First comment here.',
        ]);

        ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $mostDiscussed->id,
            'body' => 'Second comment here.',
        ]);

        $response = $this->actingAs($user)->get(route('forum.index', ['sort' => 'most_commented']));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Most discussed',
            'Less discussed',
        ]);
    }

    public function test_forum_posts_can_be_sorted_by_like_count(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $lessLiked = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Less liked post',
            'body' => 'A post with fewer likes.',
        ]);

        $moreLiked = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'More liked post',
            'body' => 'A post that should rank first by likes.',
        ]);

        $lessLiked->likes()->attach($user->id);
        $moreLiked->likes()->attach([$user->id, $otherUser->id]);

        $response = $this->actingAs($user)->get(route('forum.index', ['sort' => 'most_liked']));

        $response->assertOk();
        $response->assertSeeInOrder([
            'More liked post',
            'Less liked post',
        ]);
    }

    public function test_forum_pagination_keeps_search_query(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 11; $i++) {
            ForumPost::query()->create([
                'user_id' => $user->id,
                'title' => 'Writing search result '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'body' => 'Searchable content for pagination verification in the forum.',
            ]);
        }

        $response = $this->actingAs($user)->get(route('forum.index', ['search' => 'Writing search result']));

        $response->assertOk()
            ->assertSeeText('Writing search result 01')
            ->assertSeeText('Writing search result 10')
            ->assertDontSeeText('Writing search result 11')
            ->assertSee('value="Writing search result"', false);

        $this->actingAs($user)
            ->get(route('forum.index', ['search' => 'Writing search result', 'page' => 2]))
            ->assertOk()
            ->assertSeeText('Writing search result 11')
            ->assertDontSeeText('Writing search result 01');
    }

    public function test_forum_can_filter_by_multiple_tags(): void
    {
        $user = User::factory()->create();
        $writing = ForumTag::query()->create([
            'user_id' => $user->id,
            'name' => 'Writing',
            'slug' => 'writing',
        ]);
        $reading = ForumTag::query()->create([
            'user_id' => $user->id,
            'name' => 'Reading',
            'slug' => 'reading',
        ]);
        $speaking = ForumTag::query()->create([
            'user_id' => $user->id,
            'name' => 'Speaking',
            'slug' => 'speaking',
        ]);

        ForumPost::query()->create([
            'user_id' => $user->id,
            'forum_tag_id' => $writing->id,
            'title' => 'Writing tag post',
            'body' => 'This post belongs to the writing tag.',
        ]);

        ForumPost::query()->create([
            'user_id' => $user->id,
            'forum_tag_id' => $reading->id,
            'title' => 'Reading tag post',
            'body' => 'This post belongs to the reading tag.',
        ]);

        ForumPost::query()->create([
            'user_id' => $user->id,
            'forum_tag_id' => $speaking->id,
            'title' => 'Speaking tag post',
            'body' => 'This post belongs to the speaking tag.',
        ]);

        $this->actingAs($user)
            ->get(route('forum.index', ['tags' => [$writing->id, $reading->id]]))
            ->assertOk()
            ->assertSee('Writing tag post')
            ->assertSee('Reading tag post')
            ->assertDontSee('Speaking tag post');
    }

    public function test_forum_can_show_only_my_posts(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'My own forum post',
            'body' => 'This post should be visible in the mine filter.',
        ]);

        ForumPost::query()->create([
            'user_id' => $otherUser->id,
            'title' => 'Another user post',
            'body' => 'This post should be hidden in the mine filter.',
        ]);

        $this->actingAs($user)
            ->get(route('forum.index', ['author' => 'mine']))
            ->assertOk()
            ->assertSee('My own forum post')
            ->assertDontSee('Another user post');
    }

    public function test_forum_can_show_posts_commented_by_me_and_highlight_search(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $commentedPost = ForumPost::query()->create([
            'user_id' => $otherUser->id,
            'title' => 'Essay checklist for task response',
            'body' => 'This checklist helps organize your ideas before you draft.',
        ]);

        $notCommentedPost = ForumPost::query()->create([
            'user_id' => $otherUser->id,
            'title' => 'Vocabulary bank',
            'body' => 'This post should not appear in the participated filter.',
        ]);

        ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $commentedPost->id,
            'body' => 'I used this checklist yesterday.',
        ]);

        $this->actingAs($user)
            ->get(route('forum.index', ['author' => 'participated', 'search' => 'checklist']))
            ->assertOk()
            ->assertSee('Essay')
            ->assertDontSee('Vocabulary bank')
            ->assertSee('<mark class="rounded bg-[#F3E7D8] px-1 text-[#6B3D2E]">checklist</mark>', false);
    }

    public function test_user_can_like_and_favorite_a_forum_post(): void
    {
        $user = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Forum post for reactions',
            'body' => 'This post is used to test likes and favorites.',
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.like', $post))
            ->assertRedirect();

        $this->assertDatabaseHas('forum_post_likes', [
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.favorite', $post))
            ->assertRedirect();

        $this->assertDatabaseHas('forum_post_favorites', [
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.like', $post))
            ->assertRedirect();

        $this->assertDatabaseMissing('forum_post_likes', [
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
        ]);
    }

    public function test_user_can_edit_own_post_and_comment(): void
    {
        $user = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Original post title',
            'body' => 'Original post body content for editing.',
        ]);

        $comment = ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
            'body' => 'Original comment body.',
        ]);

        $this->actingAs($user)
            ->patch(route('forum.posts.update', $post), [
                'title' => 'Updated post title',
                'body' => 'Updated post body content after editing.',
                'forum_tag_id' => null,
            ])
            ->assertRedirect(route('forum.posts.show', $post));

        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'title' => 'Updated post title',
        ]);
        $this->assertSame('public-forum', $post->fresh()->tag?->slug);

        $this->actingAs($user)
            ->patch(route('forum.comments.update', $comment), [
                'body' => 'Updated comment body.',
            ])
            ->assertRedirect(route('forum.posts.show', $post).'#comment-'.$comment->id);

        $this->assertDatabaseHas('forum_comments', [
            'id' => $comment->id,
            'body' => 'Updated comment body.',
        ]);
    }

    public function test_user_cannot_edit_other_users_post_or_comment(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $owner->id,
            'title' => 'Owner title',
            'body' => 'Owner body for authorization checks.',
        ]);

        $comment = ForumComment::query()->create([
            'user_id' => $owner->id,
            'forum_post_id' => $post->id,
            'body' => 'Owner comment.',
        ]);

        $this->actingAs($user)
            ->patch(route('forum.posts.update', $post), [
                'title' => 'Malicious update',
                'body' => 'This should not be allowed.',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->patch(route('forum.comments.update', $comment), [
                'body' => 'This should not be allowed either.',
            ])
            ->assertForbidden();
    }

    public function test_forum_post_show_can_sort_comments_by_latest(): void
    {
        $user = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Comment sorting post',
            'body' => 'Used to verify comment sorting.',
        ]);

        $olderComment = ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
            'body' => 'Older comment',
        ]);

        $newerComment = ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
            'body' => 'Newer comment',
        ]);

        $olderComment->timestamps = false;
        $olderComment->forceFill([
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ])->saveQuietly();

        $newerComment->timestamps = false;
        $newerComment->forceFill([
            'created_at' => now(),
            'updated_at' => now(),
        ])->saveQuietly();

        $this->actingAs($user)
            ->get(route('forum.posts.show', ['post' => $post, 'comments' => 'latest']))
            ->assertOk()
            ->assertSeeInOrder([
                'Newer comment',
                'Older comment',
            ]);
    }

    public function test_user_can_reply_to_a_comment_and_store_reference(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $owner->id,
            'title' => 'Reply target post',
            'body' => 'Used to test quoted reply comments.',
        ]);

        $parentComment = ForumComment::query()->create([
            'user_id' => $owner->id,
            'forum_post_id' => $post->id,
            'body' => 'Original parent comment.',
        ]);

        $this->actingAs($user)
            ->post(route('forum.comments.store', $post), [
                'body' => 'This is a reply comment.',
                'reply_to_comment_id' => $parentComment->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('forum_comments', [
            'forum_post_id' => $post->id,
            'reply_to_comment_id' => $parentComment->id,
            'body' => 'This is a reply comment.',
        ]);

        $this->actingAs($user)
            ->get(route('forum.posts.show', $post))
            ->assertOk()
            ->assertSee('Replying to')
            ->assertSee('Original parent comment.');
    }

    public function test_forum_post_show_paginates_comments(): void
    {
        $user = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Paginated comments post',
            'body' => 'Used to test comment pagination.',
        ]);

        for ($i = 1; $i <= 9; $i++) {
            ForumComment::query()->create([
                'user_id' => $user->id,
                'forum_post_id' => $post->id,
                'body' => 'Comment page '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
            ]);
        }

        $this->actingAs($user)
            ->get(route('forum.posts.show', $post))
            ->assertOk()
            ->assertSeeText('Comment page 01')
            ->assertSeeText('Comment page 08')
            ->assertDontSeeText('Comment page 09');

        $this->actingAs($user)
            ->get(route('forum.posts.show', ['post' => $post, 'page' => 2]))
            ->assertOk()
            ->assertSeeText('Comment page 09')
            ->assertDontSeeText('Comment page 01');
    }

    public function test_saved_forum_posts_are_visible_on_saved_posts_page(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $author->id,
            'title' => 'Saved dashboard post',
            'body' => 'This saved post should appear on the dashboard.',
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.favorite', $post))
            ->assertRedirect();

        $this->actingAs($user)
            ->get(route('forum.saved'))
            ->assertOk()
            ->assertSee('Saved dashboard post');
    }

    public function test_saved_forum_posts_page_shows_only_saved_posts(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $savedPost = ForumPost::query()->create([
            'user_id' => $author->id,
            'title' => 'Saved page target',
            'body' => 'This post should appear on the saved posts page.',
        ]);

        ForumPost::query()->create([
            'user_id' => $author->id,
            'title' => 'Unsaved post',
            'body' => 'This post should not appear on the saved posts page.',
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.favorite', $savedPost))
            ->assertRedirect();

        $this->actingAs($user)
            ->get(route('forum.saved'))
            ->assertOk()
            ->assertSee('Saved page target')
            ->assertDontSee('Unsaved post');
    }

    public function test_saved_forum_posts_page_can_filter_by_tag(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $writingTag = ForumTag::query()->create([
            'user_id' => $author->id,
            'name' => 'Writing',
            'slug' => 'writing',
        ]);
        $readingTag = ForumTag::query()->create([
            'user_id' => $author->id,
            'name' => 'Reading',
            'slug' => 'reading',
        ]);

        $savedWritingPost = ForumPost::query()->create([
            'user_id' => $author->id,
            'forum_tag_id' => $writingTag->id,
            'title' => 'Saved writing post',
            'body' => 'This saved post belongs to the writing tag.',
        ]);

        $savedReadingPost = ForumPost::query()->create([
            'user_id' => $author->id,
            'forum_tag_id' => $readingTag->id,
            'title' => 'Saved reading post',
            'body' => 'This saved post belongs to the reading tag.',
        ]);

        $this->actingAs($user)->post(route('forum.posts.favorite', $savedWritingPost))->assertRedirect();
        $this->actingAs($user)->post(route('forum.posts.favorite', $savedReadingPost))->assertRedirect();

        $this->actingAs($user)
            ->get(route('forum.saved', ['tags' => [$writingTag->id]]))
            ->assertOk()
            ->assertSee('Saved writing post')
            ->assertDontSee('Saved reading post');
    }

    public function test_forum_index_timeframe_filter_excludes_old_posts(): void
    {
        $user = User::factory()->create();

        $recentPost = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Recent hot post',
            'body' => 'This post should stay visible inside the recent timeframe.',
        ]);

        $oldPost = ForumPost::query()->create([
            'user_id' => $user->id,
            'title' => 'Old hot post',
            'body' => 'This post should disappear from the recent timeframe.',
        ]);

        $recentPost->timestamps = false;
        $recentPost->forceFill([
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ])->saveQuietly();

        $oldPost->timestamps = false;
        $oldPost->forceFill([
            'created_at' => now()->subDays(12),
            'updated_at' => now()->subDays(12),
        ])->saveQuietly();

        $this->actingAs($user)
            ->get(route('forum.index', ['timeframe' => '7d']))
            ->assertOk()
            ->assertSee('Recent hot post')
            ->assertDontSee('Old hot post');
    }

    public function test_forum_post_show_can_filter_comments_to_author_only(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $author->id,
            'title' => 'Author filter post',
            'body' => 'Used to test author-only comment filtering.',
        ]);

        ForumComment::query()->create([
            'user_id' => $author->id,
            'forum_post_id' => $post->id,
            'body' => 'Comment from the original author.',
        ]);

        ForumComment::query()->create([
            'user_id' => $otherUser->id,
            'forum_post_id' => $post->id,
            'body' => 'Comment from another learner.',
        ]);

        $this->actingAs($author)
            ->get(route('forum.posts.show', ['post' => $post, 'comment_filter' => 'author']))
            ->assertOk()
            ->assertSee('Comment from the original author.')
            ->assertDontSee('Comment from another learner.')
            ->assertSee('Only Author');
    }

    public function test_forum_post_show_can_filter_comments_to_my_replies_only(): void
    {
        $author = User::factory()->create();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $author->id,
            'title' => 'My replies filter post',
            'body' => 'Used to test filtering comments to the current user.',
        ]);

        ForumComment::query()->create([
            'user_id' => $user->id,
            'forum_post_id' => $post->id,
            'body' => 'This is my own reply.',
        ]);

        ForumComment::query()->create([
            'user_id' => $otherUser->id,
            'forum_post_id' => $post->id,
            'body' => 'This is another user reply.',
        ]);

        $this->actingAs($user)
            ->get(route('forum.posts.show', ['post' => $post, 'comment_filter' => 'mine']))
            ->assertOk()
            ->assertSee('This is my own reply.')
            ->assertDontSee('This is another user reply.')
            ->assertSee('Only My Replies');
    }

    public function test_post_without_selected_tag_is_assigned_to_public_forum(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('forum.posts.store'), [
                'title' => 'General study reflection',
                'body' => 'I wanted to share a general study update without choosing a specific tag.',
            ])
            ->assertRedirect();

        $post = ForumPost::query()->latest('id')->first();

        $this->assertNotNull($post);
        $this->assertSame('public-forum', $post->tag?->slug);
        $this->assertSame('Public Forum', $post->tag?->name);
    }

    public function test_deleting_a_tag_moves_posts_to_public_forum(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create();
        $tag = ForumTag::query()->create([
            'user_id' => $author->id,
            'name' => 'Essay Ideas',
            'slug' => 'essay-ideas',
        ]);

        $post = ForumPost::query()->create([
            'user_id' => $author->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Tag migration post',
            'body' => 'This post should move into Public Forum after its tag is deleted.',
        ]);

        $this->actingAs($admin)
            ->delete(route('forum.tags.destroy', $tag))
            ->assertRedirect();

        $this->assertDatabaseMissing('forum_tags', ['id' => $tag->id]);
        $this->assertSame('public-forum', $post->fresh()->tag?->slug);
    }

    public function test_user_can_create_post_with_multiple_photos(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $firstPhoto = UploadedFile::fake()->create('study-note-1.png', 120, 'image/png');
        $secondPhoto = UploadedFile::fake()->create('study-note-2.jpg', 160, 'image/jpeg');

        $this->actingAs($user)
            ->post(route('forum.posts.store'), [
                'title' => 'Post with image attachment',
                'body' => 'This forum post includes an image attachment for discussion and review.',
                'attachments' => [$firstPhoto, $secondPhoto],
            ])
            ->assertRedirect();

        $post = ForumPost::query()->with('attachments')->latest('id')->first();

        $this->assertNotNull($post);
        $this->assertCount(2, $post->attachments);
        $this->assertSame('study-note-1.png', $post->attachments[0]->original_name);
        $this->assertSame('study-note-2.jpg', $post->attachments[1]->original_name);
        Storage::disk('public')->assertExists($post->attachments[0]->path);
        Storage::disk('public')->assertExists($post->attachments[1]->path);

        $this->actingAs($user)
            ->get(route('forum.posts.show', $post))
            ->assertOk()
            ->assertSee('study-note-1.png')
            ->assertSee('study-note-2.jpg');
    }

    public function test_deleting_post_removes_post_and_comment_photos(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $postFiles = [
            UploadedFile::fake()->create('post-photo-1.jpg', 200, 'image/jpeg'),
            UploadedFile::fake()->create('post-photo-2.png', 180, 'image/png'),
        ];
        $commentFiles = [
            UploadedFile::fake()->create('comment-photo-1.jpg', 160, 'image/jpeg'),
            UploadedFile::fake()->create('comment-photo-2.webp', 140, 'image/webp'),
        ];

        $this->actingAs($user)
            ->post(route('forum.posts.store'), [
                'title' => 'Post with removable attachment',
                'body' => 'This post is used to verify attachment cleanup during deletion.',
                'attachments' => $postFiles,
            ])
            ->assertRedirect();

        $post = ForumPost::query()->with('attachments')->latest('id')->firstOrFail();

        $this->actingAs($user)
            ->post(route('forum.comments.store', $post), [
                'body' => 'This comment also carries an attachment.',
                'attachments' => $commentFiles,
            ])
            ->assertRedirect();

        $comment = ForumComment::query()->with('attachments')->latest('id')->firstOrFail();

        foreach ($post->attachments as $attachment) {
            Storage::disk('public')->assertExists($attachment->path);
        }

        foreach ($comment->attachments as $attachment) {
            Storage::disk('public')->assertExists($attachment->path);
        }

        $this->actingAs($user)
            ->delete(route('forum.posts.destroy', $post))
            ->assertRedirect(route('forum.index'));

        foreach ($post->attachments as $attachment) {
            Storage::disk('public')->assertMissing($attachment->path);
        }

        foreach ($comment->attachments as $attachment) {
            Storage::disk('public')->assertMissing($attachment->path);
        }
    }

    public function test_tag_owner_can_pin_post_and_pinned_post_appears_first(): void
    {
        $tagOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $tag = ForumTag::query()->create([
            'user_id' => $tagOwner->id,
            'name' => 'Pinned Writing',
            'slug' => 'pinned-writing',
        ]);

        $unpinnedPost = ForumPost::query()->create([
            'user_id' => $otherUser->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Regular post',
            'body' => 'This post should appear after the pinned post.',
        ]);

        $pinnedPost = ForumPost::query()->create([
            'user_id' => $otherUser->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Pinned candidate',
            'body' => 'This post should move to the top after pinning.',
        ]);

        $this->actingAs($tagOwner)
            ->post(route('forum.posts.pin', $pinnedPost))
            ->assertRedirect();

        $this->assertTrue($pinnedPost->fresh()->is_pinned);

        $this->actingAs($tagOwner)
            ->get(route('forum.index', ['tag' => $tag->slug]))
            ->assertOk()
            ->assertSeeInOrder([
                'Pinned candidate',
                'Regular post',
            ])
            ->assertSee('Pinned');

        $this->assertFalse($unpinnedPost->fresh()->is_pinned);
    }

    public function test_non_tag_owner_cannot_pin_post(): void
    {
        $tagOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $tag = ForumTag::query()->create([
            'user_id' => $tagOwner->id,
            'name' => 'Authorization Tag',
            'slug' => 'authorization-tag',
        ]);

        $post = ForumPost::query()->create([
            'user_id' => $tagOwner->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Protected pin post',
            'body' => 'Only the tag owner or an admin may pin this post.',
        ]);

        $this->actingAs($otherUser)
            ->post(route('forum.posts.pin', $post))
            ->assertForbidden();

        $this->assertFalse($post->fresh()->is_pinned);
    }

    public function test_post_owner_can_pin_comment_and_pinned_comment_appears_first(): void
    {
        $postOwner = User::factory()->create();
        $commentAuthor = User::factory()->create();
        $post = ForumPost::query()->create([
            'user_id' => $postOwner->id,
            'title' => 'Comment pin permissions',
            'body' => 'The post owner should be able to pin comments here.',
        ]);

        $regularComment = ForumComment::query()->create([
            'user_id' => $commentAuthor->id,
            'forum_post_id' => $post->id,
            'body' => 'Regular comment that should stay below the pinned one.',
        ]);

        $pinnedComment = ForumComment::query()->create([
            'user_id' => $commentAuthor->id,
            'forum_post_id' => $post->id,
            'body' => 'Pinned comment that should move to the top.',
        ]);

        $this->actingAs($postOwner)
            ->post(route('forum.comments.pin', $pinnedComment), [
                'comments' => 'oldest',
                'comment_filter' => 'all',
                'page' => 1,
            ])
            ->assertRedirect(route('forum.posts.show', $post).'#comment-'.$pinnedComment->id);

        $this->assertTrue($pinnedComment->fresh()->is_pinned);

        $this->actingAs($postOwner)
            ->get(route('forum.posts.show', $post))
            ->assertOk()
            ->assertSeeInOrder([
                'Pinned comment that should move to the top.',
                'Regular comment that should stay below the pinned one.',
            ])
            ->assertSee('Pinned');

        $this->assertFalse($regularComment->fresh()->is_pinned);
    }

    public function test_admin_can_pin_any_post_and_comment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $tagOwner = User::factory()->create();
        $postOwner = User::factory()->create();
        $tag = ForumTag::query()->create([
            'user_id' => $tagOwner->id,
            'name' => 'Admin Pin Tag',
            'slug' => 'admin-pin-tag',
        ]);

        $post = ForumPost::query()->create([
            'user_id' => $postOwner->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Admin pin target post',
            'body' => 'An administrator should be able to pin this post.',
        ]);

        $comment = ForumComment::query()->create([
            'user_id' => $tagOwner->id,
            'forum_post_id' => $post->id,
            'body' => 'An administrator should also be able to pin this comment.',
        ]);

        $this->actingAs($admin)
            ->post(route('forum.posts.pin', $post))
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('forum.comments.pin', $comment), [
                'comments' => 'oldest',
                'comment_filter' => 'all',
                'page' => 1,
            ])
            ->assertRedirect(route('forum.posts.show', $post).'#comment-'.$comment->id);

        $this->assertTrue($post->fresh()->is_pinned);
        $this->assertTrue($comment->fresh()->is_pinned);
    }

    public function test_post_owner_receives_notification_when_someone_comments(): void
    {
        $postOwner = User::factory()->create();
        $commenter = User::factory()->create();

        $post = ForumPost::query()->create([
            'user_id' => $postOwner->id,
            'title' => 'Need feedback on this idea',
            'body' => 'I am trying to improve my summary writing with timed practice.',
        ]);

        $this->actingAs($commenter)
            ->post(route('forum.comments.store', $post), [
                'body' => 'You could compare your own summary with the source after each practice round.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('forum_notifications', [
            'user_id' => $postOwner->id,
            'actor_id' => $commenter->id,
            'forum_post_id' => $post->id,
            'type' => 'post_commented',
        ]);

        $this->actingAs($postOwner)
            ->get(route('forum.my'))
            ->assertOk()
            ->assertSee('1 new comment')
            ->assertSee('Need feedback on this idea');

        $this->actingAs($postOwner)
            ->get(route('forum.posts.show', ['post' => $post, 'notification_post' => 1]))
            ->assertOk();

        $this->assertNotNull(
            ForumNotification::query()
                ->where('user_id', $postOwner->id)
                ->where('type', 'post_commented')
                ->first()?->read_at
        );
    }

    public function test_comment_author_sees_reply_dot_on_my_forum_and_read_state_clears_when_opening_comment(): void
    {
        $postOwner = User::factory()->create();
        $commentAuthor = User::factory()->create();
        $replier = User::factory()->create();

        $post = ForumPost::query()->create([
            'user_id' => $postOwner->id,
            'title' => 'Reading strategy exchange',
            'body' => 'Share one method that helps you understand long passages better.',
        ]);

        $comment = ForumComment::query()->create([
            'user_id' => $commentAuthor->id,
            'forum_post_id' => $post->id,
            'body' => 'I annotate the main claim of each paragraph while reading.',
        ]);

        $this->actingAs($replier)
            ->post(route('forum.comments.store', $post), [
                'body' => 'I do something similar, but I also write one quick summary sentence.',
                'reply_to_comment_id' => $comment->id,
            ])
            ->assertRedirect();

        $notification = ForumNotification::query()
            ->where('user_id', $commentAuthor->id)
            ->where('type', 'comment_replied')
            ->first();

        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $this->actingAs($commentAuthor)
            ->get(route('forum.my'))
            ->assertOk()
            ->assertSee('My Forum')
            ->assertSee('1 new reply')
            ->assertSee('Reading strategy exchange');

        $this->assertNull($notification->fresh()->read_at);

        $this->actingAs($commentAuthor)
            ->get(route('forum.posts.show', ['post' => $post, 'notification_comment' => $comment->id]))
            ->assertOk();

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
