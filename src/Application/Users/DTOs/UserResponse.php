<?php

declare(strict_types=1);

namespace Src\Application\Users\DTOs;

class UserResponse
{
    public function __construct(
        public string $id,
        public string $email,
        public string $displayName,
        public int $role,
        public string $status,
        public ?string $photoUrl,
        public ?string $phoneNumber,
        public ?string $birthDate,
        public ?string $gender,
        public bool $isEmailVerified,
        public bool $isPhoneVerified,
        public string $locale,
        public ?string $countryCode
    ) {
    }
}
