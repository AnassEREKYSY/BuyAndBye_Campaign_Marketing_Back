<?php

declare(strict_types=1);

namespace Src\Application\Auth\DTOs;

class RegisterRequest
{
    public function __construct(
        public string $email,
        public string $password,
        public string $displayName,
        public ?string $photoUrl = null
    ) {
    }
}
