<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WordleWordSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sql/wordle_words_seed.sql');

        if (! file_exists($path)) {
            throw new \RuntimeException("Wordle dataset file not found: {$path}");
        }

        $sql = file_get_contents($path);

        if ($sql === false) {
            throw new \RuntimeException("Unable to read wordle dataset file: {$path}");
        }

        $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql) ?? $sql;
        preg_match_all("/'([A-Z]{5})',\s*1,\s*NOW\(\),\s*NOW\(\)/", $sql, $matches);
        $words = collect($matches[1] ?? [])
            ->map(fn (string $word) => strtoupper(trim($word)))
            ->filter(fn (string $word) => strlen($word) === 5)
            ->unique()
            ->values();

        if ($words->isEmpty()) {
            throw new \RuntimeException('No valid wordle words were parsed from database/sql/wordle_words_seed.sql.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('wordle_words')->truncate();

            $timestamp = now();

            $words
                ->chunk(1000)
                ->each(function ($chunk) use ($timestamp) {
                    DB::table('wordle_words')->insert(
                        $chunk->map(fn (string $word) => [
                            'word' => $word,
                            'is_active' => true,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ])->all()
                    );
                });
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command?->info('Wordle words seeded successfully: '.$words->count().' entries.');
    }
}
