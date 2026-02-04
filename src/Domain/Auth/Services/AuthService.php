<?php

declare(strict_types=1);

namespace Src\Domain\Auth\Services;

interface AuthServiceInterface
{
    public function hashPassword(string $password): string;

    public function verifyPassword(string $password, string $hash): bool;

    public function createTokenForUserId(string $userId): string;

    public function getTokenExpirationMinutes(): ?int;
}
