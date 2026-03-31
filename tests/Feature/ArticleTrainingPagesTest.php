<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
