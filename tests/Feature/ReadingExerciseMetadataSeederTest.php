<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingExerciseMetadataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_reading_metadata_seeder_adds_explanation_and_source_excerpt(): void
    {
        $article = Article::query()->create([
            'title' => 'Mixed Methods in Research',
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
                    ['key' => 'B', 'text' => 'Because mixed methods always reduce cost.'],
                    ['key' => 'C', 'text' => 'Because mixed methods eliminate uncertainty.'],
                    ['key' => 'D', 'text' => 'Because mixed methods avoid evidence collection.'],
                ],
            ],
            'answer' => 'A',
        ]);

        $this->seed(\Database\Seeders\ReadingExerciseMetadataSeeder::class);

        $exercise->refresh();

        $this->assertSame('Detail', $exercise->question_data['question_type']);
        $this->assertNotEmpty($exercise->question_data['explanation']);
        $this->assertNotEmpty($exercise->question_data['source_excerpt']);
        $this->assertSame(
            'Researchers choose mixed methods when they need both numerical trends and personal experiences.',
            $exercise->question_data['source_excerpt']
        );
    }
}
