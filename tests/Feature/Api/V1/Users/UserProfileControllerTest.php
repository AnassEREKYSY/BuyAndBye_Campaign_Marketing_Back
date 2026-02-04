<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Src\Domain\Shared\Enums\AccountStatus;
use Src\Domain\Shared\Enums\UserRole;
use Src\Infrastructure\Persistence\Eloquent\Models\User;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_profile_buyer(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/v1/profile/complete', [
            'display_name' => 'Buyer Updated',
            'buyer_categories' => ['tech'],
            'buyer_interests' => ['collectibles'],
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'status' => AccountStatus::Active->value,
        ]);
    }

    public function test_complete_profile_seller(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
        ]);

        Sanctum::actingAs($seller);

        $response = $this->postJson('/api/v1/profile/complete', [
            'display_name' => 'Seller Updated',
            'store_name' => 'Store',
            'company_name' => 'Company',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('seller_profiles', ['user_id' => $seller->id]);
    }

    public function test_get_profile_and_status(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
        ]);

        Sanctum::actingAs($buyer);

        $this->getJson('/api/v1/profile/get-profile')->assertOk();
        $this->getJson('/api/v1/profile/status')->assertOk();
    }

    public function test_skip_profile(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/v1/profile/skip');

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'status' => AccountStatus::Skipped->value,
        ]);
    }

    public function test_update_profile(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->putJson('/api/v1/profile/update', [
            'display_name' => 'Buyer Updated',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'display_name' => 'Buyer Updated',
        ]);
    }
}
