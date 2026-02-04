<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Src\Domain\Auth\Services\GoogleAuthServiceInterface;
use Src\Domain\Shared\Enums\UserRole;
use Src\Infrastructure\Persistence\Eloquent\Models\User;
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

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', ['email' => 'buyer@example.com']);
    }

    public function test_register_user_duplicate_email(): void
    {
        User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'buyer@example.com',
            'password' => 'password123',
            'display_name' => 'Buyer',
        ]);

        $response->assertStatus(409)->assertJsonPath('success', false);
    }

    public function test_login_success(): void
    {
        User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
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
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401)->assertJsonPath('success', false);
    }

    public function test_google_login_creates_user(): void
    {
        $this->app->bind(GoogleAuthServiceInterface::class, fn () => new class implements GoogleAuthServiceInterface {
            public function verifyIdToken(string $idToken): array
            {
                return [
                    'email' => 'google@example.com',
                    'name' => 'Google User',
                    'photo_url' => null,
                ];
            }
        });

        $response = $this->postJson('/api/v1/auth/google', [
            'idToken' => 'valid-token',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', ['email' => 'google@example.com']);
    }

    public function test_get_authenticated_user_info(): void
    {
        $user = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_role_authorization(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
        ]);

        Sanctum::actingAs($buyer);

        $this->getJson('/api/v1/auth/buyer-only')->assertOk();
        $this->getJson('/api/v1/auth/seller-only')->assertStatus(403);
    }
}
