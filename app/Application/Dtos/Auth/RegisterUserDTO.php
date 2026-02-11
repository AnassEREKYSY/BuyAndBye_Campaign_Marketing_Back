<?php

namespace App\Application\Dtos\Auth;

final class RegisterUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $displayName,
        public readonly ?string $photoUrl,
    ) {}
}