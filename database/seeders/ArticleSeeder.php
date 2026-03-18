<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\LoadsSqlInserts;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    use LoadsSqlInserts;

    /**
     * Seed articles from database/sql/english_reading.sql.
     */
    public function run(): void
    {
        $this->seedTablesFromSql(['articles']);
    }
}
