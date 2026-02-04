<?php

declare(strict_types=1);

namespace Src\Domain\Auth\Services;

interface GoogleAuthServiceInterface
{
    /**
     * @return array{email: string, name: string|null, photo_url: string|null}
     */
    public function verifyIdToken(string $idToken): array;
}
