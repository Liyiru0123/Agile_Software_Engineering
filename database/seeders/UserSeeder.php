<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\LoadsSqlInserts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    use LoadsSqlInserts;

    /**
     * Seed the application's users from database/sql/english_reading.sql.
     */
    public function run(): void
    {
        $this->seedTablesFromSql(['users']);

        // Runnable-Version-1 requires an admin account to access backend pages.
        $hasAdmin = DB::table('users')->where('is_admin', 1)->exists();

        if (!$hasAdmin) {
            $firstUserId = DB::table('users')->orderBy('user_id')->value('user_id');

            if ($firstUserId !== null) {
                DB::table('users')->where('user_id', $firstUserId)->update(['is_admin' => 1]);
            }
        }
    }
}
