<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\GeminiAudioService;
use App\Services\SpeakingExerciseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTrainingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_detail_page_loads_with_training_cards(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Safety Basics',
            'content' => 'Bridges must carry weight safely. Engineers inspect them often. Maintenance prevents major failures.',
            'difficulty' => 2,
            'word_count' => 12,
        ]);

        $response = $this->actingAs($user)->get(route('articles.show', $article));

        $response->assertOk()
            ->assertSee('Choose a skill to train')
            ->assertSee('Listening')
            ->assertSee('Writing');
    }

    public function test_listening_page_uses_existing_database_exercise(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Research Methodology',
            'content' => 'Research methodology is the backbone of any academic study. However, researchers must choose methods carefully. Therefore, strong evidence supports mixed methods in some contexts.',
            'difficulty' => 2,
            'word_count' => 24,
        ]);
        Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => 'Listen to the audio and complete the passage.',
                'blanks' => [
                    [
                        'index' => 1,
                        'context' => 'Research methodology is the _____ of any academic study.',
                    ],
                    [
                        'index' => 2,
                        'context' => '_____, researchers must choose methods carefully.',
                    ],
                ],
            ],
            'answer' => [
                '1' => ['backbone'],
                '2' => ['However', 'however'],
            ],
        ]);

        $response = $this->actingAs($user)->get(route('articles.listening', $article));

        $response->assertOk()
            ->assertSee('Listen to the audio and complete the passage.')
            ->assertSee('Complete')
            ->assertSee('Database exercise')
            ->assertDontSee('Fallback-generated');
    }

    public function test_listening_complete_saves_submission_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Safety',
            'content' => 'Bridges need careful maintenance.',
            'difficulty' => 2,
            'word_count' => 4,
        ]);
        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => 'Complete the passage.',
                'blanks' => [
                    [
                        'index' => 1,
                        'context' => 'Bridges need careful _____.',
                    ],
                ],
            ],
            'answer' => [
                '1' => ['maintenance'],
            ],
        ]);

        $response = $this->actingAs($user)->postJson(route('articles.listening.evaluate', $article), [
            'exercise_id' => $exercise->id,
            'answers' => [
                'items' => [
                    '1' => 'maintenance',
                ],
            ],
            'time_spent' => 12,
        ]);

        $response->assertOk()
            ->assertJsonPath('result.score', 100)
            ->assertJsonPath('result.correct_count', 1);

        $this->assertDatabaseHas((new Submission)->getTable(), [
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'article_id' => $article->id,
            'score' => '100.00',
            'time_spent' => 12,
        ]);
    }

    public function test_listening_page_restores_latest_submission_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Academic Listening',
            'content' => 'Students must evaluate evidence carefully.',
            'difficulty' => 2,
            'word_count' => 5,
        ]);
        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => 'Complete the listening blanks.',
                'blanks' => [
                    [
                        'index' => 1,
                        'context' => 'Students must _____ evidence carefully.',
                    ],
                ],
            ],
            'answer' => [
                '1' => ['evaluate'],
            ],
        ]);
        Submission::query()->create([
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'article_id' => $article->id,
            'user_answer' => [
                'items' => [
                    '1' => 'evaluate',
                ],
            ],
            'score' => 100,
            'time_spent' => 9,
            'attempt_count' => 1,
            'ai_advice' => [
                'provider' => 'database-check',
                'summary' => 'All blanks were completed correctly.',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('articles.listening', $article));

        $response->assertOk()
            ->assertSee('Your latest listening attempt has been restored.')
            ->assertSee('value="evaluate"', false)
            ->assertSee('"score":100', false);
    }

    public function test_article_plan_is_auto_completed_only_after_listening_reading_and_writing_are_all_submitted(): void
    {
        config(['services.gemini.api_key' => null]);

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Urban Farming Benefits',
            'content' => 'Urban farming improves access to fresh food in dense cities. It can also strengthen local communities and reduce transport costs.',
            'difficulty' => 2,
            'word_count' => 20,
        ]);

        $plan = UserPlan::query()->create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'plan_date' => now()->toDateString(),
            'plan_kind' => 'article',
            'title' => $article->title,
            'status' => 'pending',
        ]);

        $listeningExercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => 'Complete the listening blanks.',
                'blanks' => [
                    [
                        'index' => 1,
                        'context' => 'Urban farming improves access to fresh _____.',
                    ],
                ],
            ],
            'answer' => [
                '1' => ['food'],
            ],
        ]);

        $readingExercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'reading',
            'question_data' => [
                'question' => 'What is one benefit of urban farming mentioned in the article?',
                'options' => [
                    ['key' => 'A', 'text' => 'It improves access to fresh food in cities.'],
                    ['key' => 'B', 'text' => 'It increases international shipping costs.'],
                    ['key' => 'C', 'text' => 'It replaces every traditional farm.'],
                    ['key' => 'D', 'text' => 'It removes the need for local communities.'],
                ],
            ],
            'answer' => 'A',
        ]);

        $writingExercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'writing',
            'question_data' => [
                'task_type' => 'summary_response',
                'title' => 'Summary + Response',
                'instruction' => 'Summarize the article and respond to one benefit.',
                'requirement' => 'Write one summary paragraph and one response paragraph.',
                'source_text' => 'Urban farming improves access to fresh food in dense cities.',
                'word_limit' => ['min' => 20, 'max' => 120],
            ],
        ]);

        $this->actingAs($user)
            ->postJson(route('articles.listening.evaluate', $article), [
                'exercise_id' => $listeningExercise->id,
                'answers' => [
                    'items' => [
                        '1' => 'food',
                    ],
                ],
                'time_spent' => 10,
            ])
            ->assertOk()
            ->assertJsonPath('plan_auto_completed', false);

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->postJson(route('articles.reading.submit', $article), [
                'answers' => [
                    [
                        'question_id' => 'exercise-'.$readingExercise->id.'-1',
                        'selected' => 'A',
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('plan_auto_completed', false);

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
            'status' => 'pending',
        ]);

        $draft = 'Urban farming helps city residents get fresh food more easily. '
            .'I think it also supports stronger neighborhoods because people work together locally.';

        $this->actingAs($user)
            ->postJson(route('articles.writing.evaluate', $article), [
                'exercise_id' => $writingExercise->id,
                'draft' => $draft,
                'time_spent' => 45,
            ])
            ->assertOk()
            ->assertJsonPath('plan_auto_completed', true);

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
            'status' => 'completed',
        ]);

        $this->assertNotNull($plan->fresh()->completed_at);
    }
    public function test_listening_skill_plan_is_auto_completed_after_reaching_target_count(): void
    {
        $user = User::factory()->create();
        $articleA = Article::query()->create([
            'title' => 'City Farming One',
            'content' => 'Urban farming supports food access.',
            'difficulty' => 2,
            'word_count' => 5,
        ]);
        $articleB = Article::query()->create([
            'title' => 'City Farming Two',
            'content' => 'Community gardens can reduce transport costs.',
            'difficulty' => 2,
            'word_count' => 6,
        ]);

        $plan = UserPlan::query()->create([
            'user_id' => $user->id,
            'article_id' => null,
            'plan_date' => now()->toDateString(),
            'plan_kind' => 'skill',
            'title' => 'Listening practice x2',
            'skill_type' => 'listening',
            'target_count' => 2,
            'status' => 'pending',
        ]);

        $exerciseA = Exercise::query()->create([
            'article_id' => $articleA->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => 'Complete the listening blanks.',
                'blanks' => [
                    [
                        'index' => 1,
                        'context' => 'Urban farming supports ____ access.',
                    ],
                ],
            ],
            'answer' => [
                '1' => ['food'],
            ],
        ]);

        $exerciseB = Exercise::query()->create([
            'article_id' => $articleB->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => 'Complete the listening blanks.',
                'blanks' => [
                    [
                        'index' => 1,
                        'context' => 'Community gardens reduce transport ____.',
                    ],
                ],
            ],
            'answer' => [
                '1' => ['costs'],
            ],
        ]);

        $this->actingAs($user)
            ->postJson(route('articles.listening.evaluate', $articleA), [
                'exercise_id' => $exerciseA->id,
                'answers' => [
                    'items' => [
                        '1' => 'food',
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('skill_plan_auto_completed', false);

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->postJson(route('articles.listening.evaluate', $articleB), [
                'exercise_id' => $exerciseB->id,
                'answers' => [
                    'items' => [
                        '1' => 'costs',
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('skill_plan_auto_completed', true);

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
            'status' => 'completed',
        ]);
    }

    public function test_speaking_skill_plan_is_auto_completed_after_submission(): void
    {
        Storage::fake('public');
        config(['services.speaking.provider' => 'gemini']);

        $this->mock(GeminiAudioService::class, function ($mock) {
            $mock->shouldReceive('evaluateAudio')
                ->once()
                ->andReturn([
                    'score' => 88,
                    'fluency' => ['score' => 8.4, 'comment' => 'Smooth delivery.'],
                    'relevance' => ['score' => 8.6, 'comment' => 'Task is addressed clearly.'],
                    'pronunciation' => ['score' => 8.2, 'comment' => 'Mostly clear sounds.'],
                    'transcript' => 'Urban farming can strengthen local communities.',
                    'feedback' => 'Clear answer with stable pacing.',
                ]);
        });

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Urban Farming Benefits',
            'content' => 'Urban farming improves access to fresh food and strengthens local communities.',
            'difficulty' => 2,
            'word_count' => 11,
        ]);

        $plan = UserPlan::query()->create([
            'user_id' => $user->id,
            'article_id' => null,
            'plan_date' => now()->toDateString(),
            'plan_kind' => 'skill',
            'title' => 'Speaking practice x1',
            'skill_type' => 'speaking',
            'target_count' => 1,
            'status' => 'pending',
        ]);

        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'speaking',
            'question_data' => [
                'title' => 'Opinion Prompt',
                'instruction' => 'Explain one benefit of urban farming.',
            ],
        ]);

        $this->actingAs($user)
            ->post(route('articles.speaking.submit', $article), [
                'exercise_id' => $exercise->id,
                'practice_mode' => 'open_response',
                'audio' => UploadedFile::fake()->createWithContent('response.webm', 'fake-audio-content'),
            ], [
                'Accept' => 'application/json',
            ])
            ->assertOk()
            ->assertJsonPath('skill_plan_auto_completed', true);

        $this->assertDatabaseHas('user_plans', [
            'id' => $plan->id,
            'status' => 'completed',
        ]);
    }

    public function test_speaking_page_shows_short_shadowing_clips_and_open_response_tasks(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Autonomous Transit Systems',
            'content' => 'Autonomous transit systems could reduce congestion in large cities. They may also help elderly passengers and people with disabilities travel more independently. However, public trust depends on safety, reliability, and clear accountability when problems occur.',
            'audio_url' => 'https://example.com/demo.mp3',
            'difficulty' => 2,
            'word_count' => 33,
        ]);

        Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'speaking',
            'question_data' => [
                'title' => 'Opinion Prompt',
                'instruction' => 'Record your response to the following topic',
                'topic' => 'Would you trust autonomous public transport in your city? Explain why or why not.',
                'prep_time' => 45,
                'speak_time' => 90,
            ],
        ]);

        $response = $this->actingAs($user)->get(route('articles.speaking', $article));

        $response->assertOk()
            ->assertSee('Short Shadowing Clips')
            ->assertSee('Open Response Tasks')
            ->assertSee('Autonomous transit systems could reduce congestion in large cities.')
            ->assertSee('Would you trust autonomous public transport in your city?');
    }

    public function test_shadowing_submission_works_without_open_response_exercise(): void
    {
        Storage::fake('public');
        config(['services.speaking.provider' => 'gemini']);

        $this->mock(GeminiAudioService::class, function ($mock) {
            $mock->shouldReceive('evaluateAudio')
                ->once()
                ->andReturn([
                    'score' => 86,
                    'fluency' => ['score' => 8.2, 'comment' => 'Smooth overall.'],
                    'relevance' => ['score' => 8.4, 'comment' => 'Accurate repetition.'],
                    'pronunciation' => ['score' => 8.1, 'comment' => 'Mostly clear.'],
                    'transcript' => 'Autonomous systems could reduce congestion in large cities.',
                    'feedback' => 'Good attempt with minor wording loss.',
                ]);
        });

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Autonomous Transit Systems',
            'content' => 'Autonomous transit systems could reduce congestion in large cities. '
                .'They may also help elderly passengers and people with disabilities travel more independently. '
                .'However, public trust depends on safety, reliability, and clear accountability when problems occur.',
            'audio_url' => 'https://example.com/demo.mp3',
            'difficulty' => 2,
            'word_count' => 33,
        ]);

        $clip = app(SpeakingExerciseService::class)->buildShadowingClips($article)[0];

        $response = $this->actingAs($user)->post(route('articles.speaking.submit', $article), [
            'practice_mode' => 'shadowing',
            'shadowing_clip_id' => $clip['id'],
            'audio' => UploadedFile::fake()->createWithContent('shadowing.webm', 'fake-audio-content'),
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('evaluation.practice_mode', 'shadowing')
            ->assertJsonPath('evaluation.clip_id', $clip['id']);

        $submission = Submission::query()->firstOrFail();

        $this->assertSame('shadowing', data_get($submission->user_answer, 'practice_mode'));
        $this->assertSame($clip['id'], data_get($submission->user_answer, 'shadowing_clip_id'));
        $this->assertSame('shadowing', data_get($submission->exercise?->question_data, 'mode'));
        Storage::disk('public')->assertExists((string) data_get($submission->user_answer, 'audio_path'));
    }

    public function test_shadowing_submission_with_invalid_clip_does_not_store_audio_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Autonomous Transit Systems',
            'content' => 'Autonomous transit systems could reduce congestion in large cities. '
                .'They may also help elderly passengers and people with disabilities travel more independently.',
            'audio_url' => 'https://example.com/demo.mp3',
            'difficulty' => 2,
            'word_count' => 22,
        ]);

        $response = $this->actingAs($user)->post(route('articles.speaking.submit', $article), [
            'practice_mode' => 'shadowing',
            'shadowing_clip_id' => 'shadow-missing',
            'audio' => UploadedFile::fake()->createWithContent('shadowing.webm', 'fake-audio-content'),
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'The selected shadowing clip could not be found.');

        $this->assertSame([], Storage::disk('public')->allFiles('submissions/speaking'));
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_speaking_submission_deletes_uploaded_audio_when_ai_evaluation_fails(): void
    {
        Storage::fake('public');
        config(['services.speaking.provider' => 'gemini']);

        $this->mock(GeminiAudioService::class, function ($mock) {
            $mock->shouldReceive('evaluateAudio')
                ->once()
                ->andReturn([
                    'error' => 'AI evaluation service is temporarily unavailable.',
                ]);
        });

        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Safety',
            'content' => 'Bridges need careful maintenance and routine inspection.',
            'difficulty' => 2,
            'word_count' => 7,
        ]);
        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'speaking',
            'question_data' => [
                'title' => 'Opinion Prompt',
                'instruction' => 'Explain the main idea of the article.',
            ],
        ]);

        $response = $this->actingAs($user)->post(route('articles.speaking.submit', $article), [
            'exercise_id' => $exercise->id,
            'practice_mode' => 'open_response',
            'audio' => UploadedFile::fake()->createWithContent('response.webm', 'fake-audio-content'),
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'AI evaluation service is temporarily unavailable.');

        $this->assertSame([], Storage::disk('public')->allFiles('submissions/speaking'));
        $this->assertDatabaseCount('submissions', 0);
    }
}
