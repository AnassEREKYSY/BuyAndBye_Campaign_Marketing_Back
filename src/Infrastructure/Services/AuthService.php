<?php

declare(strict_types=1);

namespace Src\Infrastructure\Services;

use Illuminate\Support\Facades\Hash;
use Src\Domain\Auth\Services\AuthServiceInterface;
use Src\Infrastructure\Persistence\Eloquent\Models\User as UserModel;

class AuthService implements AuthServiceInterface
{
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
        $user = UserModel::query()->findOrFail($userId);

        return $user->createToken('api')->plainTextToken;
    }

    public function getTokenExpirationMinutes(): ?int
    {
        $expiration = config('sanctum.expiration');

        return is_numeric($expiration) ? (int) $expiration : null;
    }
}
