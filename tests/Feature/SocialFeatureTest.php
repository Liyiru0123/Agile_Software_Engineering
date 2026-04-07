<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\ForumTag;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_friend_request_can_be_sent_and_accepted(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender)->post(route('friends.requests.store'), [
            'receiver_id' => $receiver->id,
            'message' => 'Let us practice together.',
        ])->assertRedirect();

        $friendRequest = FriendRequest::query()->firstOrFail();

        $this->assertSame('pending', $friendRequest->status);
        $this->assertSame($sender->id, $friendRequest->sender_id);
        $this->assertSame($receiver->id, $friendRequest->receiver_id);

        $this->actingAs($receiver)->post(route('friends.requests.accept', $friendRequest))
            ->assertRedirect();

        $friendRequest->refresh();

        $this->assertSame('accepted', $friendRequest->status);
        $this->assertNotNull($friendRequest->responded_at);
        $this->assertDatabaseHas('friendships', [
            'user_one_id' => min($sender->id, $receiver->id),
            'user_two_id' => max($sender->id, $receiver->id),
        ]);
        $this->assertDatabaseHas('conversations', [
            'direct_key' => Conversation::directKeyFor($sender->id, $receiver->id),
        ]);
    }

    public function test_direct_messages_require_friendship(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender)->post(route('messages.start'), [
            'recipient_id' => $receiver->id,
        ])->assertRedirect();

        $this->assertDatabaseCount('conversations', 0);
    }

    public function test_friend_can_start_conversation_and_send_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        Friendship::query()->create([
            'user_one_id' => min($sender->id, $receiver->id),
            'user_two_id' => max($sender->id, $receiver->id),
        ]);

        $startResponse = $this->actingAs($sender)->post(route('messages.start'), [
            'recipient_id' => $receiver->id,
        ]);

        $conversation = Conversation::query()->firstOrFail();

        $startResponse->assertRedirect(route('messages.show', $conversation));

        $this->actingAs($sender)->post(route('messages.store', $conversation), [
            'body' => 'Hi, do you want to review the article together tonight?',
        ])->assertRedirect(route('messages.show', $conversation));

        $this->assertDatabaseHas('conversation_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
        ]);

        $message = ConversationMessage::query()->firstOrFail();
        $this->assertStringContainsString('review the article', $message->body);
    }

    public function test_forum_show_page_exposes_social_targets_for_post_and_comment_bodies(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $tag = ForumTag::query()->create([
            'user_id' => $author->id,
            'name' => 'Study Talk',
            'slug' => 'study-talk',
        ]);
        $post = ForumPost::query()->create([
            'user_id' => $author->id,
            'forum_tag_id' => $tag->id,
            'title' => 'Bridge ideas',
            'body' => 'I think article-based study groups help a lot with motivation.',
        ]);
        $comment = ForumComment::query()->create([
            'user_id' => $commenter->id,
            'forum_post_id' => $post->id,
            'body' => 'I agree. Shared note-taking helps me notice more detail.',
        ]);

        $response = $this->actingAs($viewer)->get(route('forum.posts.show', $post));

        $response->assertOk()
            ->assertSee('forum-social-target', false)
            ->assertSee('data-source-type="forum_post"', false)
            ->assertSee('data-source-type="forum_comment"', false)
            ->assertSee('forum-social-menu', false);
    }
}
