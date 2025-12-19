<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@saas-platform.test'],
            [
                'name' => 'Super Administrator',
                'email' => 'admin@saas-platform.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_super_admin' => true,
                'active' => true,
            ]
        );
    }
}