<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Services\ReadingQuestionMetadataBuilder;
use Illuminate\Database\Seeder;

class ReadingExerciseMetadataSeeder extends Seeder
{
    public function run(): void
    {
        $builder = app(ReadingQuestionMetadataBuilder::class);

        $updatedCount = 0;

        Exercise::query()
            ->where('type', 'reading')
            ->with('article:id,content')
            ->orderBy('article_id')
            ->get()
            ->each(function (Exercise $exercise) use ($builder, &$updatedCount) {
                if (! $exercise->article || ! is_array($exercise->question_data)) {
                    return;
                }

                $original = $exercise->question_data;
                $enriched = $builder->enrichQuestionData(
                    (string) $exercise->article->content,
                    $original,
                    $exercise->answer,
                );

                if ($enriched === $original) {
                    return;
                }

                $exercise->question_data = $enriched;
                $exercise->save();
                $updatedCount++;
            });

        $this->command?->info('Reading exercise metadata updated: '.$updatedCount);
    }
}
