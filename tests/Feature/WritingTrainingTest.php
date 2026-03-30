<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->assertDatabaseCount('exercises', 3);
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
                    'word_count',
                    'submission_id',
                    'attempt_count',
                ],
            ]);

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'exercise_id' => $exercise->id,
            'time_spent' => 95,
            'attempt_count' => 1,
        ]);
    }
}
