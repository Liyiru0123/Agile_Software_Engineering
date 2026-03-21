<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\LoadsSqlInserts;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    use LoadsSqlInserts;

    /**
     * Seed questions from database/sql/english_reading.sql.
     */
    public function run(): void
    {
        $this->seedTablesFromSql(['questions']);
    }
}
