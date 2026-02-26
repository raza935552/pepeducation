<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Primary super admin
        User::updateOrCreate(
            ['email' => 'razakkhanafridi@gmail.com'],
            [
                'name' => 'Razak Khan',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Trollwarlord@123')),
                'role' => 'admin',
            ]
        );

        // Additional admin from env (optional, for team members)
        $extraEmail = env('ADMIN_EMAIL');
        if ($extraEmail && $extraEmail !== 'razakkhanafridi@gmail.com') {
            User::updateOrCreate(
                ['email' => $extraEmail],
                [
                    'name' => 'Admin',
                    'password' => Hash::make(env('ADMIN_PASSWORD', 'change-me-immediately')),
                    'role' => 'admin',
                ]
            );
        }
    }
}
