<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AiPromptSeeder::class,
            ArticleSeeder::class,
            VocabularySeeder::class,
            AdminUserSeeder::class,
        ]);
        
        $this->command->info('✅ All test data seeded successfully!');
        $this->command->info('📚 Articles: ' . \App\Models\Article::count());
        $this->command->info('📝 Exercises: ' . \App\Models\Exercise::count());
        $this->command->info('📖 Vocabulary: ' . \App\Models\Vocabulary::count());
    }
}
