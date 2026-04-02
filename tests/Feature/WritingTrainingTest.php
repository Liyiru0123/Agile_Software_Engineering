<?php

namespace Tests\Feature;

use App\Models\AiPrompt;
use App\Models\Article;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WritingTrainingTest extends TestCase
{
    use RefreshDatabase;

    public function test_writing_page_creates_multiple_task_types_for_an_article(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Academic Collaboration in Online Courses',
            'content' => 'Online courses increasingly depend on collaborative writing tasks. Students must summarize research, respond to peers, and revise drafts based on feedback. Strong collaboration helps learners develop academic language and critical thinking.',
            'difficulty' => 2,
            'word_count' => 29,
        ]);

        $response = $this->actingAs($user)->get(route('articles.writing', $article));

        $response->assertOk()
            ->assertSee('Summary + Response')
            ->assertSee('Paraphrase Studio')
            ->assertSee('Opinion Builder')
            ->assertSee('Submit for review');

        $this->assertSame(
            3,
            Exercise::query()->where('article_id', $article->id)->where('type', 'writing')->count()
        );
    }

    public function test_writing_submission_uses_local_rubric_and_persists_submission(): void
    {
        config(['services.gemini.api_key' => null]);

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Using Peer Feedback in Writing',
            'content' => 'Peer feedback can improve academic writing when students review each other carefully. It helps writers identify unclear arguments, missing evidence, and weak organization. Teachers should train students to give specific and respectful feedback.',
            'difficulty' => 2,
            'word_count' => 34,
        ]);

        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'writing',
            'question_data' => [
                'task_type' => 'summary_response',
                'title' => 'Summary + Response',
                'instruction' => 'Summarize the article and explain how peer feedback can help student writers.',
                'requirement' => 'Write one summary section and one response section.',
                'source_text' => 'Peer feedback can improve academic writing when students review each other carefully.',
                'word_limit' => ['min' => 60, 'max' => 120],
            ],
        ]);

        $draft = 'Peer feedback improves writing because students can notice weak logic and missing evidence. '
            .'In my view, it is especially useful when teachers show students how to give specific advice. '
            .'As a result, writers can revise more clearly and confidently.';

        $response = $this->actingAs($user)->postJson(route('articles.writing.evaluate', $article), [
            'exercise_id' => $exercise->id,
            'draft' => $draft,
            'time_spent' => 95,
        ]);

        $response->assertOk()
            ->assertJsonPath('result.provider', 'local-rubric')
            ->assertJsonPath('result.exercise_id', $exercise->id)
            ->assertJsonStructure([
                'result' => [
                    'score',
                    'summary',
                    'breakdown',
                    'strengths',
                    'improvements',
                    'suggested_revision',
                    'word_range',
                    'ai_diagnostics',
                    'word_count',
                    'submission_id',
                    'attempt_count',
                ],
            ]);
    }

    public function test_writing_submission_can_use_gemini_compatible_gateway(): void
    {
        File::delete(storage_path('logs/writing-ai-raw.log'));

        config([
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.base_url' => 'https://moyu.info/v1',
            'services.gemini.model' => 'gemini-2.5-flash',
        ]);

        Http::fake([
            'https://moyu.info/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'score' => 91,
                                'summary' => 'A strong response with accurate article coverage and clear organization.',
                                'breakdown' => [
                                    ['key' => 'task_achievement', 'label' => 'Task Achievement', 'score' => 23, 'max' => 25, 'feedback' => 'The response addresses the prompt directly.'],
                                    ['key' => 'coherence', 'label' => 'Coherence & Cohesion', 'score' => 22, 'max' => 25, 'feedback' => 'Ideas progress in a clear sequence.'],
                                    ['key' => 'lexical_resource', 'label' => 'Lexical Resource', 'score' => 23, 'max' => 25, 'feedback' => 'Vocabulary is varied and mostly precise.'],
                                    ['key' => 'grammar', 'label' => 'Grammar & Accuracy', 'score' => 23, 'max' => 25, 'feedback' => 'Grammar is generally accurate with only minor slips.'],
                                ],
                                'strengths' => ['Clear summary of the source text.', 'Effective academic tone.'],
                                'improvements' => ['Add one more concrete supporting example.'],
                                'suggested_revision' => 'Tighten the second paragraph and add one more sentence linking the article to your own view.',
                            ], JSON_UNESCAPED_SLASHES),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'AI Feedback for Draft Improvement',
            'content' => 'Students improve more quickly when feedback is timely, specific, and connected to revision goals. Teachers can use AI tools to surface language issues, but human guidance is still needed to help students prioritize revisions.',
            'difficulty' => 2,
            'word_count' => 31,
        ]);

        $prompt = AiPrompt::query()->create([
            'type' => 'writing',
            'prompt' => 'Evaluate the writing response and return a score plus structured feedback.',
        ]);

        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'writing',
            'question_data' => [
                'task_type' => 'opinion',
                'title' => 'Opinion Builder',
                'instruction' => 'Explain whether AI feedback helps students revise more effectively.',
                'requirement' => 'State your view and connect it to the article.',
                'source_text' => 'Students improve more quickly when feedback is timely, specific, and connected to revision goals.',
                'word_limit' => ['min' => 60, 'max' => 120],
            ],
            'ai_prompt_id' => $prompt->id,
        ]);

        $draft = 'I believe AI feedback can help students revise more effectively because it gives quick and specific comments. '
            .'However, teachers are still necessary because students need help deciding which problems matter most. '
            .'In my view, the best approach is to combine AI support with human guidance.';

        $response = $this->actingAs($user)->postJson(route('articles.writing.evaluate', $article), [
            'exercise_id' => $exercise->id,
            'draft' => $draft,
            'time_spent' => 88,
        ]);

        $response->assertOk()
            ->assertJsonPath('result.provider', 'gemini')
            ->assertJsonPath('result.score', 91)
            ->assertJsonPath('result.summary', 'A strong response with accurate article coverage and clear organization.');

        $this->assertTrue(File::exists(storage_path('logs/writing-ai-raw.log')));
        $this->assertStringContainsString(
            '"provider":"gemini-compatible"',
            File::get(storage_path('logs/writing-ai-raw.log'))
        );

        Http::assertSent(function ($request) {
            return $request->url() === 'https://moyu.info/v1/chat/completions'
                && $request['model'] === 'gemini-2.5-flash';
        });
    }

    public function test_writing_submission_reports_ai_fallback_diagnostics_when_gemini_is_unreachable(): void
    {
        config([
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.base_url' => 'https://moyu.info/v1',
            'services.gemini.model' => 'gemini-2.5-flash',
        ]);

        Http::fake([
            'https://moyu.info/v1/chat/completions' => function () {
                throw new ConnectionException('cURL error 7: Failed to connect to AI gateway.');
            },
        ]);

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Revision Timing and Feedback',
            'content' => 'Students revise more effectively when feedback arrives quickly and gives them a clear next step. Delayed or vague advice often reduces motivation and makes revision harder to prioritize.',
            'difficulty' => 2,
            'word_count' => 29,
        ]);

        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'writing',
            'question_data' => [
                'task_type' => 'summary_response',
                'title' => 'Summary + Response',
                'instruction' => 'Summarize the article and explain why timing matters in feedback.',
                'requirement' => 'Write one summary paragraph and one response paragraph.',
                'source_text' => 'Students revise more effectively when feedback arrives quickly and gives them a clear next step.',
                'word_limit' => ['min' => 60, 'max' => 120],
            ],
        ]);

        $draft = 'Quick feedback matters because students remember their draft choices and can revise while the task is still fresh. '
            .'I think timely advice also improves motivation because learners know exactly what to fix next.';

        $response = $this->actingAs($user)->postJson(route('articles.writing.evaluate', $article), [
            'exercise_id' => $exercise->id,
            'draft' => $draft,
            'time_spent' => 54,
        ]);

        $response->assertOk()
            ->assertJsonPath('result.provider', 'local-rubric')
            ->assertJsonPath('result.ai_diagnostics.attempts.0.provider', 'gemini')
            ->assertJsonPath('result.ai_diagnostics.attempts.0.reason', 'connection_error');
    }

    public function test_writing_submission_surfaces_provider_error_message_from_gemini_payload(): void
    {
        config([
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.base_url' => 'https://moyu.info/v1',
            'services.gemini.model' => 'gemini-2.5-flash',
        ]);

        Http::fake([
            'https://moyu.info/v1/chat/completions' => Http::response([
                'error' => [
                    'message' => 'The upstream gateway is busy. Please retry later.',
                    'type' => 'server_busy',
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Feedback Prioritization',
            'content' => 'Students often receive multiple comments on a draft, but they improve faster when they learn how to prioritize the most important revisions first.',
            'difficulty' => 2,
            'word_count' => 24,
        ]);

        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'writing',
            'question_data' => [
                'task_type' => 'opinion',
                'title' => 'Opinion Builder',
                'instruction' => 'Explain how students should prioritize revision feedback.',
                'requirement' => 'State your view and support it with one reason.',
                'source_text' => 'Students improve faster when they prioritize the most important revisions first.',
                'word_limit' => ['min' => 60, 'max' => 120],
            ],
        ]);

        $draft = 'Students should start with the biggest problems in ideas and organization before fixing small grammar issues. '
            .'I believe this approach saves time and leads to clearer improvement.';

        $response = $this->actingAs($user)->postJson(route('articles.writing.evaluate', $article), [
            'exercise_id' => $exercise->id,
            'draft' => $draft,
            'time_spent' => 61,
        ]);

        $response->assertOk()
            ->assertJsonPath('result.provider', 'local-rubric')
            ->assertJsonPath('result.ai_diagnostics.attempts.0.reason', 'provider_error')
            ->assertJsonPath('result.ai_diagnostics.attempts.0.message', 'The upstream gateway is busy. Please retry later.');
    }
}
