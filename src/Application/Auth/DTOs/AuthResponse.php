<?php

declare(strict_types=1);

namespace Src\Application\Auth\DTOs;

class AuthResponse
{
    public function __construct(
        public string $token,
        public ?int $expiresIn,
        public string $userId,
        public string $profileStatus,
        public bool $isProfileComplete
    ) {
    }
}
