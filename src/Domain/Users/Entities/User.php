<?php

declare(strict_types=1);

namespace Src\Domain\Users\Entities;

use DateTimeImmutable;

class User
{
    public function __construct(
        public string $id,
        public string $email,
        public ?string $password,
        public string $displayName,
        public int $role,
        public string $status,
        public ?string $photoUrl = null,
        public ?string $phoneNumber = null,
        public ?DateTimeImmutable $birthDate = null,
        public ?string $gender = null,
        public bool $isEmailVerified = false,
        public bool $isPhoneVerified = false,
        public string $locale = 'en',
        public ?string $countryCode = null,
        public ?DateTimeImmutable $profileCompletedAt = null,
        public bool $profileSkipped = false,
        public array $followingSellerIds = [],
        public array $blockedUserIds = [],
        public ?array $buyerCategories = null,
        public ?array $buyerInterests = null,
        public ?array $paymentMethods = null,
        public ?DateTimeImmutable $lastLoginAt = null
    ) {
    }
}
