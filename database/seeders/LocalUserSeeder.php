<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\SellerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocalUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@buyandbye.com'],
            [
                'password' => Hash::make('password'),
                'display_name' => 'Test User',
                'role' => UserRole::Seller,
                'status' => AccountStatus::Active,
                'profile_completed' => true,
                'profile_skipped' => false,
            ]
        );

        UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'phone_number' => '+33612345678',
                'birth_date' => '1995-01-15',
                'gender' => 'male',
                'country_code' => 'FR',
                'locale' => 'fr',
            ]
        );

        SellerProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => 'Test Store',
                'company_name' => 'Test Company',
                'vat_number' => 'FR12345678901',
                'support_email' => 'support@teststore.com',
                'support_phone' => '+33612345678',
                'store_description' => 'A test store for local development',
            ]
        );
    }
}
