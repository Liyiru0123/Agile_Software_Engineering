<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArticleSegmentSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sql/article_segments_seed.sql');

        if (! file_exists($path)) {
            $this->command?->warn("Article segment SQL file not found: {$path}");

            return;
        }

        $rows = $this->parseRows($path);

        if ($rows->isEmpty()) {
            $this->command?->warn('No article segment rows were parsed from the SQL file.');

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('article_segments')->truncate();

            foreach ($rows->chunk(100) as $chunk) {
                DB::table('article_segments')->insert($chunk->all());
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command?->info('Article segments seeded successfully: '.$rows->count().' rows.');
    }

    protected function parseRows(string $path): Collection
    {
        $rows = collect();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open article segment SQL file: {$path}");
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);

                if ($line === '' || ! str_starts_with($line, '(')) {
                    continue;
                }

                $parsed = $this->parseSqlValueRow($line);

                if ($parsed !== null) {
                    $rows->push($parsed);
                }
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }

    protected function parseSqlValueRow(string $line): ?array
    {
        $pattern = "/^\\((\\d+),\\s*(\\d+),\\s*(\\d+),\\s*(\\d+),\\s*'((?:\\\\'|[^'])*)',\\s*(NULL|'((?:\\\\'|[^'])*)'),\\s*([0-9.]+),\\s*([0-9.]+)\\)[,;]?$/u";

        if (preg_match($pattern, $line, $matches) !== 1) {
            return null;
        }

        return [
            'id' => (int) $matches[1],
            'article_id' => (int) $matches[2],
            'paragraph_index' => (int) $matches[3],
            'sentence_index' => (int) $matches[4],
            'content_en' => $this->unescapeSqlString($matches[5]),
            'content_cn' => $matches[6] === 'NULL'
                ? null
                : $this->unescapeSqlString($matches[7]),
            'start_time' => round((float) $matches[8], 2),
            'end_time' => round((float) $matches[9], 2),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function unescapeSqlString(string $value): string
    {
        return str_replace(
            ["\\\\", "\\'"],
            ["\\", "'"],
            $value
        );
    }
}
