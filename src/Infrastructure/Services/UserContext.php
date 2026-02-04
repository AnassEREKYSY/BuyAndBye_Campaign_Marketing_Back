<?php

declare(strict_types=1);

namespace Src\Infrastructure\Services;

use Illuminate\Support\Facades\Auth;
use Src\Domain\Users\Services\UserContextInterface;

class UserContext implements UserContextInterface
{
    public function getUserId(): string
    {
        return (string) Auth::id();
    }

    public function getUserRole(): int
    {
        $user = Auth::user();

        return $user?->role ?? 0;
    }
}
