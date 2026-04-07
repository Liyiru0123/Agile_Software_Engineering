<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AiPromptSeeder::class,
            ArticleSeeder::class,
            ReadingExerciseMetadataSeeder::class,
            WritingExerciseMetadataSeeder::class,
            SpeakingExerciseSeeder::class,
            VocabularySeeder::class,
            WordleWordSeeder::class,
            AdminUserSeeder::class,
        ]);

        $this->command?->info('All seed data imported successfully.');
        $this->command?->info('Articles: '.\App\Models\Article::count());
        $this->command?->info('Exercises: '.\App\Models\Exercise::count());
        $this->command?->info('Vocabulary: '.\App\Models\Vocabulary::count());
        $this->command?->info('Wordle words: '.DB::table('wordle_words')->count());
    }
}
