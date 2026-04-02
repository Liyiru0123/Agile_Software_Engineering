<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingTrainingTest extends TestCase
{
    use RefreshDatabase;

    public function test_reading_page_renders_exercise_backed_questions_in_sidebar(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Bridge Design Choices',
            'content' => 'Suspension bridges can handle shifting wind loads better than rigid alternatives. Engineers changed the design after testing different bridge models.',
            'difficulty' => 2,
            'word_count' => 19,
        ]);

        Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'reading',
            'question_data' => [
                'question' => 'Why did the engineers change the original bridge design?',
                'options' => [
                    ['key' => 'A', 'text' => 'Because a suspension bridge handles wind and shifting loads better.'],
                    ['key' => 'B', 'text' => 'Because the harbor was already closed.'],
                    ['key' => 'C', 'text' => 'Because the bridge was too short.'],
                    ['key' => 'D', 'text' => 'Because the steel arrived late.'],
                ],
            ],
            'answer' => 'A',
        ]);

        $response = $this->actingAs($user)->get(route('articles.reading', $article));

        $response->assertOk()
            ->assertSee('Reading Questions')
            ->assertSee('Why did the engineers change the original bridge design?')
            ->assertSee('Enable Markup');
    }

    public function test_reading_submit_returns_answer_explanation_and_source_sentence(): void
    {
        $user = User::factory()->create();
        $article = Article::query()->create([
            'title' => 'Research Methods',
            'content' => 'Researchers choose mixed methods when they need both numerical trends and personal experiences. This combination provides stronger evidence in complex contexts.',
            'difficulty' => 2,
            'word_count' => 20,
        ]);

        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'reading',
            'question_data' => [
                'question' => 'Why do researchers choose mixed methods in the article?',
                'options' => [
                    ['key' => 'A', 'text' => 'Because mixed methods combine numerical trends with personal experiences.'],
                    ['key' => 'B', 'text' => 'Because mixed methods are always faster.'],
                    ['key' => 'C', 'text' => 'Because mixed methods eliminate all bias.'],
                    ['key' => 'D', 'text' => 'Because mixed methods require less evidence.'],
                ],
            ],
            'answer' => 'A',
        ]);

        $response = $this->actingAs($user)->postJson(route('articles.reading.submit', $article), [
            'answers' => [
                [
                    'question_id' => 'exercise-'.$exercise->id.'-1',
                    'selected' => 'A',
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('score', 100)
            ->assertJsonPath('correct_count', 1)
            ->assertJsonPath('results.0.correct_answer', 'A')
            ->assertJsonPath('results.0.source_anchor', 'p0-s0')
            ->assertJsonFragment([
                'source_excerpt' => 'Researchers choose mixed methods when they need both numerical trends and personal experiences.',
            ]);

        $this->assertStringContainsString(
            'The best answer is A.',
            $response->json('results.0.explanation')
        );
    }
}
