<?php

namespace Tests\Feature;

use App\Models\AiPrompt;
use App\Models\Article;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WritingExerciseMetadataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_writing_metadata_seeder_normalizes_existing_task_and_creates_missing_types(): void
    {
        AiPrompt::query()->create([
            'type' => 'writing',
            'prompt' => 'Evaluate writing.',
        ]);

        $article = Article::query()->create([
            'title' => 'Autonomous Vehicle Safety',
            'content' => 'Self-driving cars rely on cameras, radar, and software models to respond to changing road conditions. Engineers must also explain why these systems make the decisions they do.',
            'difficulty' => 2,
            'word_count' => 27,
        ]);

        Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'writing',
            'question_data' => [
                'instruction' => 'Rewrite the following paragraph to reduce similarity',
                'requirement' => 'Reduce similarity below 30% while keeping the original meaning',
                'source_text' => 'Self-driving cars rely on cameras, radar, and software models to respond to changing road conditions.',
                'word_limit' => ['min' => 40, 'max' => 100],
            ],
            'answer' => null,
        ]);

        $this->seed(\Database\Seeders\WritingExerciseMetadataSeeder::class);

        $tasks = Exercise::query()
            ->where('article_id', $article->id)
            ->where('type', 'writing')
            ->orderBy('id')
            ->get();

        $this->assertCount(3, $tasks);

        $taskTypes = $tasks->map(fn (Exercise $exercise) => $exercise->question_data['task_type'] ?? null)->all();
        sort($taskTypes);

        $this->assertSame(
            ['opinion', 'paraphrase', 'summary_response'],
            $taskTypes
        );

        $paraphraseTask = $tasks->first(fn (Exercise $exercise) => ($exercise->question_data['task_type'] ?? null) === 'paraphrase');
        $summaryTask = $tasks->first(fn (Exercise $exercise) => ($exercise->question_data['task_type'] ?? null) === 'summary_response');
        $opinionTask = $tasks->first(fn (Exercise $exercise) => ($exercise->question_data['task_type'] ?? null) === 'opinion');

        $this->assertSame('Paraphrase Studio', $paraphraseTask->question_data['title']);
        $this->assertSame('database', $paraphraseTask->question_data['provider']);
        $this->assertNotEmpty($paraphraseTask->question_data['checkpoints']);
        $this->assertNotEmpty($paraphraseTask->question_data['rubric_focus']);
        $paraphraseSourceWords = $this->countWords($paraphraseTask->question_data['source_text']);
        $summarySourceWords = $this->countWords($summaryTask->question_data['source_text']);
        $opinionSourceWords = $this->countWords($opinionTask->question_data['source_text']);
        $expectedParaphraseMin = max(20, min($paraphraseSourceWords, (int) floor($paraphraseSourceWords * 0.8)));
        $expectedParaphraseMax = max($expectedParaphraseMin + 6, min(90, (int) ceil($paraphraseSourceWords * 1.25)));
        $expectedSummaryMin = max($summarySourceWords < 50 ? 60 : 90, min(160, (int) round($summarySourceWords * 1.35)));
        $expectedSummaryMax = max($expectedSummaryMin + ($summarySourceWords < 50 ? 25 : 30), min(220, (int) round($summarySourceWords * 1.8)));
        $expectedOpinionMin = max($opinionSourceWords < 45 ? 50 : 80, min(145, (int) round($opinionSourceWords * 1.1)));
        $expectedOpinionMax = max($expectedOpinionMin + ($opinionSourceWords < 45 ? 25 : 30), min(205, (int) round($opinionSourceWords * 1.45)));

        $this->assertGreaterThanOrEqual(20, $paraphraseSourceWords);
        $this->assertSame($expectedParaphraseMin, (int) $paraphraseTask->question_data['word_limit']['min']);
        $this->assertSame($expectedParaphraseMax, (int) $paraphraseTask->question_data['word_limit']['max']);
        $this->assertGreaterThanOrEqual(27, $summarySourceWords);
        $this->assertGreaterThanOrEqual(27, $opinionSourceWords);
        $this->assertSame($expectedSummaryMin, (int) $summaryTask->question_data['word_limit']['min']);
        $this->assertSame($expectedSummaryMax, (int) $summaryTask->question_data['word_limit']['max']);
        $this->assertSame($expectedOpinionMin, (int) $opinionTask->question_data['word_limit']['min']);
        $this->assertSame($expectedOpinionMax, (int) $opinionTask->question_data['word_limit']['max']);
    }

    protected function countWords(string $text): int
    {
        preg_match_all("/[A-Za-z][A-Za-z0-9'-]*/", $text, $matches);

        return count($matches[0] ?? []);
    }
}
