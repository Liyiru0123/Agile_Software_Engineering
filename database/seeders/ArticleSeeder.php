<?php

namespace Database\Seeders;

use App\Models\AiPrompt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $datasetPath = database_path('sql/version3.21/generated_article_exercise_dataset.json');

        if (! file_exists($datasetPath)) {
            throw new \RuntimeException("Dataset file not found: {$datasetPath}");
        }

        $dataset = json_decode(file_get_contents($datasetPath), true);

        if (! is_array($dataset)) {
            throw new \RuntimeException('Dataset file is not valid JSON.');
        }

        $speakingPromptId = AiPrompt::query()->where('type', 'speaking')->value('id');
        $writingPromptId = AiPrompt::query()->where('type', 'writing')->value('id');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('submissions')->truncate();
            DB::table('user_favorites')->truncate();
            DB::table('exercises')->truncate();
            DB::table('articles')->truncate();

            foreach ($dataset as $entry) {
                $article = $entry['article'] ?? null;
                $exercises = $entry['exercises'] ?? [];

                if (! is_array($article) || ! isset($article['article_id'], $article['title'], $article['content'])) {
                    continue;
                }

                DB::table('articles')->insert([
                    'id' => (int) $article['article_id'],
                    'title' => (string) $article['title'],
                    'content' => (string) $article['content'],
                    'audio_url' => $article['audio_url'] ?? null,
                    'difficulty' => (int) ($article['difficulty'] ?? 1),
                    'word_count' => (int) ($article['word_count'] ?? 0),
                ]);

                foreach ($exercises as $exercise) {
                    if (! is_array($exercise) || ! isset($exercise['type'], $exercise['question_data'])) {
                        continue;
                    }

                    $promptId = $exercise['ai_prompt_id'] ?? null;

                    if ($promptId === null) {
                        $promptId = match ($exercise['type']) {
                            'speaking' => $speakingPromptId,
                            'writing' => $writingPromptId,
                            default => null,
                        };
                    }

                    DB::table('exercises')->insert([
                        'article_id' => (int) $article['article_id'],
                        'type' => (string) $exercise['type'],
                        'question_data' => json_encode($exercise['question_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'answer' => $exercise['answer'] === null
                            ? null
                            : json_encode($exercise['answer'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'ai_prompt_id' => $promptId,
                    ]);
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command?->info('Articles and exercises imported from JSON dataset successfully.');
        $this->command?->info('Articles: '.DB::table('articles')->count());
        $this->command?->info('Exercises: '.DB::table('exercises')->count());
    }
}
