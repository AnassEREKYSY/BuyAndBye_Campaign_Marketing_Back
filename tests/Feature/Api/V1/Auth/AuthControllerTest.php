<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1\Auth;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user_success(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'buyer@example.com',
            'password' => 'password123',
            'display_name' => 'Buyer',
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', ['email' => 'buyer@example.com']);
    }

    public function test_register_user_duplicate_email(): void
    {
        User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Incomplete->value,
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'buyer@example.com',
            'password' => 'password123',
            'display_name' => 'Buyer',
        ]);

        $response->assertStatus(409);
    }

    public function test_login_success(): void
    {
        User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Incomplete->value,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_login_invalid_credentials(): void
    {
        User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Incomplete->value,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_get_authenticated_user_info(): void
    {
        $user = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Active->value,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertOk();
    }
}
