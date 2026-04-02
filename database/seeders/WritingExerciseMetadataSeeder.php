<?php

namespace Database\Seeders;

use App\Models\AiPrompt;
use App\Models\Article;
use App\Models\Exercise;
use App\Services\WritingTaskMetadataBuilder;
use Illuminate\Database\Seeder;

class WritingExerciseMetadataSeeder extends Seeder
{
    public function run(): void
    {
        $builder = app(WritingTaskMetadataBuilder::class);
        $writingPromptId = AiPrompt::query()->where('type', 'writing')->value('id');

        $updatedCount = 0;
        $createdCount = 0;

        Article::query()
            ->orderBy('id')
            ->get()
            ->each(function (Article $article) use ($builder, $writingPromptId, &$updatedCount, &$createdCount) {
                $existingTypes = [];

                $article->exercises()
                    ->where('type', 'writing')
                    ->orderBy('id')
                    ->get()
                    ->each(function (Exercise $exercise) use ($article, $builder, &$existingTypes, &$updatedCount, $writingPromptId) {
                        if (! is_array($exercise->question_data)) {
                            return;
                        }

                        $original = $exercise->question_data;
                        $normalized = $builder->enrichTaskData($article->content, $original, 'database');
                        $existingTypes[] = $normalized['task_type'];

                        $dirty = $normalized !== $original || (int) $exercise->ai_prompt_id !== (int) $writingPromptId;

                        if (! $dirty) {
                            return;
                        }

                        $exercise->question_data = $normalized;
                        $exercise->ai_prompt_id = $writingPromptId;
                        $exercise->save();
                        $updatedCount++;
                    });

                foreach ($builder->buildMissingTasks($article->content, array_values(array_unique($existingTypes))) as $task) {
                    Exercise::query()->create([
                        'article_id' => $article->id,
                        'type' => 'writing',
                        'question_data' => $task,
                        'answer' => null,
                        'ai_prompt_id' => $writingPromptId,
                    ]);
                    $createdCount++;
                }
            });

        $this->command?->info('Writing exercise metadata updated: '.$updatedCount);
        $this->command?->info('Missing writing tasks created: '.$createdCount);
    }
}
