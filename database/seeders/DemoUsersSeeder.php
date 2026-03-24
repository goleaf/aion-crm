<?php

namespace Database\Seeders;

use App\Modules\Shared\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config()->array('demo-users.users') as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => $user['password'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
