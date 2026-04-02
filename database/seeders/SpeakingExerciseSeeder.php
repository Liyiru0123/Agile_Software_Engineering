<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class SpeakingExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = Article::all();

        if ($articles->isEmpty()) {
            $this->command->warn('No articles found. Please run ArticleSeeder first.');
            return;
        }

        foreach ($articles as $article) {
            // Check if a speaking exercise already exists for this article
            $exists = Exercise::where('article_id', $article->id)
                ->where('type', 'speaking')
                ->exists();

            if (!$exists) {
                Exercise::create([
                    'article_id' => $article->id,
                    'type' => 'speaking',
                    'question_data' => [
                        'title' => 'Main Idea Summary',
                        'instruction' => 'Summarize the central claim and main points of this article in your own words. Aim for a 45-60 second spoken response.',
                    ],
                    'answer' => null, // Speaking usually doesn't have a fixed answer or uses AI evaluation
                ]);
            }
        }

        $this->command->info('Speaking exercises added successfully for all articles.');
    }
}
