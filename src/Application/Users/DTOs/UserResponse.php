<?php

declare(strict_types=1);

namespace Src\Application\Users\DTOs;
use Src\Domain\Shared\Enums\UserRole;

class UserResponse
{
    public function __construct(
        public string $id,
        public string $email,
        public string $displayName,
        public UserRole $role,
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
