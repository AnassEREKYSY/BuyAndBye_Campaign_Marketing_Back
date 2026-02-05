<?php

declare(strict_types=1);

namespace Src\Domain\Users\Entities;

use DateTimeImmutable;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Shared\Enums\AccountStatus;

class User
{
    public function __construct(
        public string $id,
        public string $email,
        public ?string $password,
        public ?string $displayName,
        public UserRole $role,
        public AccountStatus $status,
        public ?string $photoUrl = null,
        public bool $profileCompleted = false,
        public bool $profileSkipped = false,
        public ?DateTimeImmutable $emailVerifiedAt = null,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,
    ) {}
}
