<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1\Users;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
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
            'status' => AccountStatus::Incomplete->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/v1/profile/complete', [
            'display_name' => 'Buyer Updated',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'status' => AccountStatus::Active->value,
        ]);
    }

    public function test_get_profile_and_status(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Active->value,
        ]);

        Sanctum::actingAs($buyer);

        $this->getJson('/api/v1/profile')->assertOk();
        $this->getJson('/api/v1/profile/status')->assertOk();
    }

    public function test_skip_profile(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Incomplete->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/v1/profile/skip');

        $response->assertOk();
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
            'status' => AccountStatus::Active->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->putJson('/api/v1/profile', [
            'display_name' => 'Buyer Updated',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'display_name' => 'Buyer Updated',
        ]);
    }
}
