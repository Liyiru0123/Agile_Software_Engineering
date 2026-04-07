<?php

namespace Database\Seeders;

use App\Models\ForumTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@eaplus.local'],
            [
                'name' => 'EAPlus Admin',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        ForumTag::query()->updateOrCreate(
            ['slug' => 'public-forum'],
            [
                'user_id' => $admin->id,
                'name' => 'Public Forum',
                'description' => 'Open discussion for general learning reflections, questions, and study updates.',
            ]
        );

        $this->command?->info('Admin user ready: admin@eaplus.local / Admin123!');
    }
}
