<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\LoadsSqlInserts;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use LoadsSqlInserts;

    /**
     * Seed the application's users from database/sql/english_reading.sql.
     */
    public function run(): void
    {
        $this->seedTablesFromSql(['users']);
    }
}
