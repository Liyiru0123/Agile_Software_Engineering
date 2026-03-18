<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\LoadsSqlInserts;
use Illuminate\Database\Seeder;

class ArticleTagSeeder extends Seeder
{
    use LoadsSqlInserts;

    /**
     * Seed article-tag relationships from database/sql/english_reading.sql.
     */
    public function run(): void
    {
        $this->seedTablesFromSql(['article_tags']);
    }
}
