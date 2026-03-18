<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\LoadsSqlInserts;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    use LoadsSqlInserts;

    /**
     * Seed tags from database/sql/english_reading.sql.
     */
    public function run(): void
    {
        $this->seedTablesFromSql(['tags']);
    }
}
