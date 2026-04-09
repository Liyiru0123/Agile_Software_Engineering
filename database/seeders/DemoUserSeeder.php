<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\CompanionInventory;
use App\Models\CompanionProfile;
use App\Models\CompanionShopItem;
use App\Models\CompanionTransaction;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\DailyAttendance;
use App\Models\Exercise;
use App\Models\ForumComment;
use App\Models\ForumNotification;
use App\Models\ForumPost;
use App\Models\ForumTag;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\ReadingHistory;
use App\Models\SelectionFavorite;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserPlan;
use App\Models\UserPresence;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@eaplus.local')->first();

        if (! $admin) {
            $this->command?->warn('Admin user not found. Skipping demo account seed.');

            return;
        }

        $demo = User::query()->updateOrCreate(
            ['email' => 'demo@eaplus.local'],
            [
                'name' => 'Demo Learner',
                'password' => Hash::make('Demo123!'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ]
        );

        $buddy = User::query()->updateOrCreate(
            ['email' => 'buddy@eaplus.local'],
            [
                'name' => 'Study Buddy',
                'password' => Hash::make('Demo123!'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ]
        );

        $publicTag = ForumTag::query()->updateOrCreate(
            ['slug' => 'public-forum'],
            [
                'user_id' => $admin->id,
                'name' => 'Public Forum',
                'description' => 'Open discussion for general learning reflections, questions, and study updates.',
            ]
        );

        $writingTag = ForumTag::query()->updateOrCreate(
            ['slug' => 'writing-workshop-demo'],
            [
                'user_id' => $demo->id,
                'name' => 'Writing Workshop',
                'description' => 'A demo tag for sharing writing plans, summaries, and revision notes.',
            ]
        );

        $articles = Article::query()->orderBy('id')->take(6)->get();
        $exerciseByType = Exercise::query()
            ->whereIn('type', ['listening', 'speaking', 'reading', 'writing'])
            ->orderBy('id')
            ->get()
            ->groupBy('type')
            ->map(fn (Collection $group) => $group->first());

        if ($articles->count() < 4 || $exerciseByType->count() < 4) {
            $this->command?->warn('Not enough article or exercise seed data. Skipping demo account seed.');

            return;
        }

        DB::transaction(function () use ($admin, $demo, $buddy, $publicTag, $writingTag, $articles, $exerciseByType) {
            $today = now()->startOfDay();

            $this->seedPlans($demo, $articles, $today);
            $this->seedAttendanceAndCompanion($demo, $today);
            $this->seedFavorites($demo, $articles);
            $this->seedReadingHistory($demo, $articles, $today);
            $this->seedNotebook($demo, $articles, $today);
            $this->seedSubmissions($demo, $exerciseByType, $today);
            $this->seedForum($admin, $demo, $buddy, $publicTag, $writingTag, $today);
            $this->seedSocial($admin, $demo, $buddy, $today);
        });

        $this->command?->info('Demo user ready: demo@eaplus.local / Demo123!');
        $this->command?->info('Buddy user ready: buddy@eaplus.local / Demo123!');
    }

    protected function seedPlans(User $demo, Collection $articles, Carbon $today): void
    {
        UserPlan::query()->where('user_id', $demo->id)->delete();

        $records = [
            [
                'user_id' => $demo->id,
                'article_id' => $articles[0]->id,
                'plan_date' => $today->copy()->subDay()->toDateString(),
                'plan_kind' => 'article',
                'title' => null,
                'skill_type' => null,
                'target_count' => null,
                'status' => 'completed',
                'completed_at' => $today->copy()->subDay()->setTime(20, 15),
            ],
            [
                'user_id' => $demo->id,
                'article_id' => $articles[1]->id,
                'plan_date' => $today->copy()->subDay()->toDateString(),
                'plan_kind' => 'article',
                'title' => null,
                'skill_type' => null,
                'target_count' => null,
                'status' => 'pending',
                'completed_at' => null,
            ],
            [
                'user_id' => $demo->id,
                'article_id' => null,
                'plan_date' => $today->toDateString(),
                'plan_kind' => 'skill',
                'title' => 'Listening practice x2',
                'skill_type' => 'listening',
                'target_count' => 2,
                'status' => 'pending',
                'completed_at' => null,
            ],
            [
                'user_id' => $demo->id,
                'article_id' => null,
                'plan_date' => $today->toDateString(),
                'plan_kind' => 'skill',
                'title' => 'Speaking practice x1',
                'skill_type' => 'speaking',
                'target_count' => 1,
                'status' => 'completed',
                'completed_at' => $today->copy()->setTime(9, 40),
            ],
            [
                'user_id' => $demo->id,
                'article_id' => null,
                'plan_date' => $today->copy()->addDay()->toDateString(),
                'plan_kind' => 'custom',
                'title' => 'Revise one writing draft and add better topic sentences',
                'skill_type' => null,
                'target_count' => null,
                'status' => 'pending',
                'completed_at' => null,
            ],
            [
                'user_id' => $demo->id,
                'article_id' => $articles[2]->id,
                'plan_date' => $today->copy()->addDays(2)->toDateString(),
                'plan_kind' => 'article',
                'title' => null,
                'skill_type' => null,
                'target_count' => null,
                'status' => 'skipped',
                'completed_at' => null,
            ],
        ];

        DB::table('user_plans')->insert($records);
    }

    protected function seedAttendanceAndCompanion(User $demo, Carbon $today): void
    {
        DailyAttendance::query()->where('user_id', $demo->id)->delete();
        CompanionInventory::query()->where('user_id', $demo->id)->delete();
        CompanionTransaction::query()->where('user_id', $demo->id)->delete();

        $items = CompanionShopItem::query()
            ->whereIn('slug', ['library-cape', 'study-lantern', 'storybook-badge', 'makeup-checkin-card'])
            ->get()
            ->keyBy('slug');

        $equippedItemId = $items->get('library-cape')?->id;

        CompanionProfile::query()->updateOrCreate(
            ['user_id' => $demo->id],
            [
                'gold' => 540,
                'total_gold_earned' => 920,
                'equipped_shop_item_id' => $equippedItemId,
                'last_daily_reward_at' => $today->copy()->setTime(8, 10),
            ]
        );

        foreach ([
            [$today->copy()->subDays(6), 'claim', 25, null],
            [$today->copy()->subDays(4), 'claim', 25, null],
            [$today->copy()->subDays(3), 'makeup', 25, $items->get('makeup-checkin-card')?->id],
            [$today->copy()->subDay(), 'claim', 25, null],
            [$today->copy(), 'claim', 25, null],
        ] as [$date, $source, $rewardAmount, $shopItemId]) {
            DailyAttendance::query()->updateOrCreate(
                [
                    'user_id' => $demo->id,
                    'attendance_date' => $date->toDateString(),
                ],
                [
                    'source' => $source,
                    'reward_amount' => $rewardAmount,
                    'shop_item_id' => $shopItemId,
                ]
            );
        }

        foreach ([
            ['library-cape', 1, $today->copy()->subDays(10), null],
            ['study-lantern', 1, $today->copy()->subDays(7), null],
            ['storybook-badge', 1, $today->copy()->subDays(5), null],
            ['makeup-checkin-card', 2, $today->copy()->subDays(2), $today->copy()->subDay()->setTime(21, 0)],
        ] as [$slug, $quantity, $purchasedAt, $lastUsedAt]) {
            $item = $items->get($slug);
            if (! $item) {
                continue;
            }

            CompanionInventory::query()->create([
                'user_id' => $demo->id,
                'shop_item_id' => $item->id,
                'quantity' => $quantity,
                'purchased_at' => $purchasedAt,
                'last_used_at' => $lastUsedAt,
            ]);
        }

        foreach ([
            ['earn', 'listening_complete', 80, 'demo-listening-streak', ['label' => 'Listening streak'], $today->copy()->subDays(5)->setTime(19, 20)],
            ['earn', 'writing_complete', 110, 'demo-writing-draft', ['label' => 'Writing draft'], $today->copy()->subDays(4)->setTime(20, 5)],
            ['earn', 'daily_checkin', 25, 'demo-checkin-1', ['label' => 'Daily check-in'], $today->copy()->subDays(3)->setTime(8, 0)],
            ['spend', 'shop_purchase', -180, 'demo-shop-cape', ['item' => 'Library Cape'], $today->copy()->subDays(2)->setTime(18, 15)],
            ['earn', 'reading_complete', 65, 'demo-reading-finish', ['label' => 'Reading finish'], $today->copy()->subDay()->setTime(17, 30)],
            ['earn', 'daily_checkin', 25, 'demo-checkin-2', ['label' => 'Daily check-in'], $today->copy()->setTime(8, 10)],
        ] as [$type, $source, $amount, $rewardKey, $meta, $createdAt]) {
            CompanionTransaction::query()->create([
                'user_id' => $demo->id,
                'type' => $type,
                'source' => $source,
                'amount' => $amount,
                'reward_key' => $rewardKey,
                'meta' => $meta,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    protected function seedFavorites(User $demo, Collection $articles): void
    {
        DB::table('user_favorites')->where('user_id', $demo->id)->delete();

        foreach ($articles->take(3) as $article) {
            DB::table('user_favorites')->insert([
                'user_id' => $demo->id,
                'article_id' => $article->id,
                'created_at' => now(),
            ]);
        }
    }

    protected function seedReadingHistory(User $demo, Collection $articles, Carbon $today): void
    {
        ReadingHistory::query()->where('user_id', $demo->id)->delete();

        $pages = ['reading', 'listening', 'writing', 'speaking'];

        foreach ($articles->take(4)->values() as $index => $article) {
            ReadingHistory::query()->create([
                'user_id' => $demo->id,
                'article_id' => $article->id,
                'last_page' => $pages[$index] ?? 'article',
                'is_completed' => $index < 2,
                'visit_count' => 2 + $index,
                'first_viewed_at' => $today->copy()->subDays(6 - $index)->setTime(19, 0),
                'last_viewed_at' => $today->copy()->subDays(3 - min($index, 3))->setTime(20, 15),
                'created_at' => $today->copy()->subDays(6 - $index)->setTime(19, 0),
                'updated_at' => $today->copy()->subDays(3 - min($index, 3))->setTime(20, 15),
            ]);
        }
    }

    protected function seedNotebook(User $demo, Collection $articles, Carbon $today): void
    {
        SelectionFavorite::query()->where('user_id', $demo->id)->delete();

        foreach ($articles->take(3)->values() as $index => $article) {
            $paragraphs = $this->extractParagraphs($article);
            $paragraph = $paragraphs[$index] ?? ($paragraphs[0] ?? $article->content ?? $article->title);
            $selectedText = Str::limit(Str::squish($paragraph), 120, '');

            DB::table('selection_favorites')->insert([
                'user_id' => $demo->id,
                'article_id' => $article->id,
                'paragraph_index' => $index,
                'selected_text' => $selectedText,
                'translated_text' => 'Demo translation note: focus on sentence structure, transition signals, and precise verbs.',
                'paragraph_text' => $paragraph,
                'source_language' => 'en',
                'target_language' => 'zh-CN',
                'provider' => 'demo-seed',
                'created_at' => $today->copy()->subDays(2 - min($index, 2))->setTime(21, 10),
            ]);
        }
    }

    protected function seedSubmissions(User $demo, Collection $exerciseByType, Carbon $today): void
    {
        Submission::query()->where('user_id', $demo->id)->delete();

        $payloads = [
            'listening' => [
                'user_answer' => ['choice' => 'B', 'notes' => 'The speaker changed tone in the final section.'],
                'score' => 86.0,
                'time_spent' => 880,
                'ai_advice' => [
                    'summary' => 'Strong keyword recognition and good detail capture.',
                    'next_step' => 'Replay the ending once and track contrast markers more carefully.',
                ],
            ],
            'speaking' => [
                'user_answer' => ['transcript' => 'I shadowed the main paragraph and focused on stress and pacing.'],
                'score' => 90.0,
                'time_spent' => 620,
                'ai_advice' => [
                    'summary' => 'Clear pacing with confident delivery.',
                    'next_step' => 'Stretch the stressed syllables a little more on key nouns.',
                ],
            ],
            'reading' => [
                'user_answer' => ['answers' => ['1' => 'C', '2' => 'A', '3' => 'D']],
                'score' => 84.0,
                'time_spent' => 940,
                'ai_advice' => [
                    'summary' => 'Good control of detail questions and locating support.',
                    'next_step' => 'Double-check inference questions before locking the final answer.',
                ],
            ],
            'writing' => [
                'user_answer' => [
                    'text' => 'The article shows that regular practice leads to stronger long-term learning outcomes, and I agree because short routines are easier to maintain and improve over time.',
                ],
                'score' => 88.0,
                'time_spent' => 1320,
                'ai_advice' => [
                    'summary' => 'Clear position and solid organization.',
                    'next_step' => 'Add one more concrete supporting example to strengthen the body paragraph.',
                ],
            ],
        ];

        foreach ($payloads as $type => $submission) {
            /** @var Exercise|null $exercise */
            $exercise = $exerciseByType->get($type);
            if (! $exercise) {
                continue;
            }

            DB::table('submissions')->insert([
                'user_id' => $demo->id,
                'exercise_id' => $exercise->id,
                'article_id' => $exercise->article_id,
                'user_answer' => json_encode($submission['user_answer'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'score' => $submission['score'],
                'time_spent' => $submission['time_spent'],
                'attempt_count' => 1,
                'ai_advice' => json_encode($submission['ai_advice'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $today->copy()->subDays(match ($type) {
                    'listening' => 4,
                    'speaking' => 2,
                    'reading' => 1,
                    default => 0,
                })->setTime(19, 45),
            ]);
        }
    }

    protected function seedForum(User $admin, User $demo, User $buddy, ForumTag $publicTag, ForumTag $writingTag, Carbon $today): void
    {
        ForumNotification::query()->where('user_id', $demo->id)->delete();

        $demoPost = ForumPost::query()->updateOrCreate(
            [
                'user_id' => $demo->id,
                'title' => 'How I turn article reading into better writing notes',
            ],
            [
                'forum_tag_id' => $writingTag->id,
                'source_name' => 'Demo Learner',
                'body' => implode("\n\n", [
                    'After each article, I save one sentence pattern and one transition phrase before I start writing.',
                    'This makes it easier to turn reading input into a short summary or opinion response.',
                    'My next step is to compare two articles and practice paraphrasing them with the same structure.',
                ]),
                'view_count' => 37,
                'is_pinned' => true,
                'pinned_at' => $today->copy()->subDay()->setTime(18, 0),
                'created_at' => $today->copy()->subDays(2)->setTime(20, 30),
                'updated_at' => $today->copy()->subDay()->setTime(18, 0),
            ]
        );

        $publicDemoPost = ForumPost::query()->updateOrCreate(
            [
                'user_id' => $demo->id,
                'title' => 'Looking for a faster speaking shadowing routine',
            ],
            [
                'forum_tag_id' => $publicTag->id,
                'source_name' => 'Demo Learner',
                'body' => implode("\n\n", [
                    'I am trying to keep my speaking practice short and repeatable.',
                    'Right now I listen once, shadow twice, then record one final take.',
                    'If anyone has a better 10-minute routine, I would love to try it.',
                ]),
                'view_count' => 18,
                'is_pinned' => false,
                'pinned_at' => null,
                'created_at' => $today->copy()->subDay()->setTime(9, 15),
                'updated_at' => $today->copy()->subDay()->setTime(9, 15),
            ]
        );

        $adminPost = ForumPost::query()
            ->where('user_id', $admin->id)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('id')
            ->first();

        if (! $adminPost) {
            $adminPost = ForumPost::query()->create([
                'user_id' => $admin->id,
                'forum_tag_id' => $publicTag->id,
                'title' => 'Welcome to the Public Forum',
                'source_name' => 'EAPlus Admin',
                'body' => 'Use this space to share study reflections, practice ideas, and helpful feedback for other learners.',
                'view_count' => 12,
                'is_pinned' => true,
                'pinned_at' => $today->copy()->subDays(7)->setTime(10, 0),
            ]);
        }

        $buddyCommentOnDemoPost = $this->upsertComment(
            $buddy,
            $demoPost,
            'I like the sentence-pattern idea. I do something similar with topic sentences before I start a summary.',
            null,
            true,
            $today->copy()->subDay()->setTime(20, 10)
        );

        $demoCommentOnAdminPost = $this->upsertComment(
            $demo,
            $adminPost,
            'The forum examples are useful. I have been using reading articles as prompts for short writing practice.',
            null,
            false,
            $today->copy()->subDay()->setTime(13, 40)
        );

        $adminReplyToDemoComment = $this->upsertComment(
            $admin,
            $adminPost,
            'That is a strong routine. Try keeping one saved notebook line as a model sentence for the next draft.',
            $demoCommentOnAdminPost->id,
            false,
            $today->copy()->setTime(8, 20)
        );

        $publicReply = $this->upsertComment(
            $buddy,
            $publicDemoPost,
            'Your three-step routine sounds efficient. You could also keep one target sound and check it in the final take.',
            null,
            false,
            $today->copy()->setTime(11, 10)
        );

        $demo->favoritedForumPosts()->sync([$adminPost->id]);
        $demo->likedForumPosts()->sync([$adminPost->id, $publicDemoPost->id]);
        $buddy->likedForumPosts()->sync([$demoPost->id, $publicDemoPost->id]);

        ForumNotification::query()->create([
            'user_id' => $demo->id,
            'actor_id' => $buddy->id,
            'forum_post_id' => $demoPost->id,
            'forum_comment_id' => $buddyCommentOnDemoPost->id,
            'target_forum_comment_id' => null,
            'type' => 'post_commented',
            'read_at' => null,
            'created_at' => $today->copy()->subDay()->setTime(20, 10),
            'updated_at' => $today->copy()->subDay()->setTime(20, 10),
        ]);

        ForumNotification::query()->create([
            'user_id' => $demo->id,
            'actor_id' => $admin->id,
            'forum_post_id' => $adminPost->id,
            'forum_comment_id' => $adminReplyToDemoComment->id,
            'target_forum_comment_id' => $demoCommentOnAdminPost->id,
            'type' => 'comment_replied',
            'read_at' => null,
            'created_at' => $today->copy()->setTime(8, 20),
            'updated_at' => $today->copy()->setTime(8, 20),
        ]);

        if ($publicReply->created_at?->greaterThan($publicDemoPost->updated_at)) {
            $publicDemoPost->forceFill(['updated_at' => $publicReply->created_at])->save();
        }
    }

    protected function seedSocial(User $admin, User $demo, User $buddy, Carbon $today): void
    {
        [$userOneId, $userTwoId] = Friendship::orderedPair($demo->id, $buddy->id);

        Friendship::query()->updateOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
        ], [
            'created_at' => $today->copy()->subDays(6)->setTime(21, 0),
            'updated_at' => $today->copy()->subDays(6)->setTime(21, 0),
        ]);

        FriendRequest::query()->where('sender_id', $admin->id)->where('receiver_id', $demo->id)->delete();
        FriendRequest::query()->create([
            'sender_id' => $admin->id,
            'receiver_id' => $demo->id,
            'status' => 'pending',
            'message' => 'Welcome to the community. Feel free to connect for feedback and study support.',
            'source_type' => 'forum_post',
            'source_id' => null,
            'responded_at' => null,
            'created_at' => $today->copy()->subHours(18),
            'updated_at' => $today->copy()->subHours(18),
        ]);

        $conversation = Conversation::firstOrCreateDirectBetween($demo, $buddy);
        $conversation->messages()->delete();

        foreach ([
            [$demo->id, 'Hey, I just finished the writing task. Do you want to compare outlines later?', $today->copy()->subHours(6)],
            [$buddy->id, 'Sure. I can also send you the short speaking routine I mentioned in the forum.', $today->copy()->subHours(5)->addMinutes(12)],
            [$buddy->id, 'I left one new message here so the unread badge shows up in the demo.', $today->copy()->subHours(1)->addMinutes(20)],
        ] as [$senderId, $body, $createdAt]) {
            ConversationMessage::query()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'body' => $body,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $conversation->forceFill([
            'last_message_at' => $today->copy()->subHours(1)->addMinutes(20),
            'updated_at' => $today->copy()->subHours(1)->addMinutes(20),
        ])->save();

        $conversation->participants()->syncWithoutDetaching([
            $demo->id => [
                'last_read_at' => $today->copy()->subHours(5),
                'updated_at' => $today->copy()->subHours(5),
            ],
            $buddy->id => [
                'last_read_at' => $today->copy()->subMinutes(20),
                'updated_at' => $today->copy()->subMinutes(20),
            ],
        ]);

        UserPresence::query()->updateOrCreate(
            ['user_id' => $buddy->id],
            [
                'current_path' => '/speaking/video-call',
                'is_video_available' => true,
                'last_seen_at' => now()->subSeconds(20),
            ]
        );

        UserPresence::query()->updateOrCreate(
            ['user_id' => $demo->id],
            [
                'current_path' => '/forum/my',
                'is_video_available' => false,
                'last_seen_at' => now()->subMinute(),
            ]
        );
    }

    protected function upsertComment(
        User $user,
        ForumPost $post,
        string $body,
        ?int $replyToCommentId,
        bool $isPinned,
        Carbon $createdAt
    ): ForumComment {
        return ForumComment::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'forum_post_id' => $post->id,
                'body' => $body,
            ],
            [
                'reply_to_comment_id' => $replyToCommentId,
                'is_pinned' => $isPinned,
                'pinned_at' => $isPinned ? $createdAt : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]
        );
    }

    /**
     * @return array<int, string>
     */
    protected function extractParagraphs(Article $article): array
    {
        $content = str_replace(["\r\n", "\r"], "\n", (string) $article->content);
        $paragraphs = preg_split("/\n\s*\n/", $content) ?: [];
        $paragraphs = array_values(array_filter(array_map(
            fn (string $paragraph) => trim($paragraph),
            $paragraphs
        )));

        if ($paragraphs !== []) {
            return $paragraphs;
        }

        $singleLine = trim($content);

        return $singleLine !== ''
            ? [$singleLine]
            : [$article->title];
    }
}
