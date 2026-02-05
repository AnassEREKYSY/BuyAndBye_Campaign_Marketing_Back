<?php

declare(strict_types=1);

namespace Src\Infrastructure\Services;

use Illuminate\Support\Facades\Auth;
use Src\Domain\Users\Services\UserContextInterface;

final class SanctumUserContext implements UserContextInterface
{
    public function getUserId(): string
    {
        $user = Auth::user();

        if (!$user) {
            throw new \RuntimeException('No authenticated user');
        }

        return (string) $user->id;
    }

    public function getUserRole(): int
    {
        $user = Auth::user();

        if (!$user) {
            throw new \RuntimeException('No authenticated user');
        }

        return (int) $user->role->value;
    }
}
