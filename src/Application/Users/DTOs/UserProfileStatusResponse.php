<?php

declare(strict_types=1);

namespace Src\Application\Users\DTOs;

class UserProfileStatusResponse
{
    public function __construct(
        public string $userId,
        public string $status,
        public bool $isProfileComplete
    ) {
    }
}
