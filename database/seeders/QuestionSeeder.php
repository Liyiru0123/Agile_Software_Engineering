<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\LoadsSqlInserts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    use LoadsSqlInserts;

    /**
     * Seed questions from database/sql/english_reading.sql.
     */
    public function run(): void
    {
        $this->seedTablesFromSql(['questions']);

        // Keep legacy SQL answers compatible with Runnable-Version-1 JSON answer handling.
        DB::table('questions')->select(['question_id', 'answer', 'type'])->orderBy('question_id')->chunk(200, function ($rows): void {
            foreach ($rows as $row) {
                $answer = $row->answer;
                $decoded = is_string($answer) ? json_decode($answer, true) : null;

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                    DB::table('questions')
                        ->where('question_id', $row->question_id)
                        ->update([
                            'answer' => json_encode([$answer], JSON_UNESCAPED_UNICODE),
                            'type' => $row->type ?: 'single',
                        ]);
                    continue;
                }

                if (empty($row->type)) {
                    DB::table('questions')
                        ->where('question_id', $row->question_id)
                        ->update(['type' => 'single']);
                }
            }
        });
    }
}
