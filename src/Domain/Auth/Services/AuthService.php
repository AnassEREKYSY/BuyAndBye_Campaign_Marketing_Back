<?php

declare(strict_types=1);

namespace Src\Domain\Auth\Services;

use Illuminate\Support\Facades\Hash;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Infrastructure\Persistence\Eloquent\Models\User;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function createTokenForUserId(string $userId): string
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \RuntimeException("User not found with ID: {$userId}");
        }

        // Get the Eloquent model to use Sanctum's createToken
        $eloquentUser = User::find($userId);
        
        if (!$eloquentUser) {
            throw new \RuntimeException("Eloquent user not found with ID: {$userId}");
        }

        return $eloquentUser->createToken('api-token')->plainTextToken;
    }

    public function getTokenExpirationMinutes(): ?int
    {
        return config('sanctum.expiration');
    }
}