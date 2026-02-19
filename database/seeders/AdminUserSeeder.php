<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@buyandbye.com'],
            [
                'display_name' => 'Admin',
                'password' => Hash::make('P@ssword123'),
                'role' => UserRole::Admin,
                'status' => AccountStatus::Active,
                'profile_completed' => true,
            ]
        );
    }
}