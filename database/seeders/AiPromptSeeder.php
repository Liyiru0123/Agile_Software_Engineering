<?php

namespace Database\Seeders;

use App\Models\AiPrompt;
use Illuminate\Database\Seeder;

class AiPromptSeeder extends Seeder
{
    public function run(): void
    {
        AiPrompt::query()->updateOrCreate(
            ['type' => 'speaking'],
            [
                'prompt' => 'Evaluate this speaking response based on: 1) Fluency and coherence, 2) Pronunciation, 3) Vocabulary range, 4) Grammatical accuracy. Provide a score from 0-100 and specific feedback for improvement.',
            ]
        );

        AiPrompt::query()->updateOrCreate(
            ['type' => 'writing'],
            [
                'prompt' => 'Evaluate this writing task based on: 1) Task achievement, 2) Coherence and cohesion, 3) Lexical resource, 4) Grammatical range and accuracy. Provide a score from 0-100 and detailed feedback.',
            ]
        );

        $this->command?->info('AI prompts seeded successfully.');
    }
}
