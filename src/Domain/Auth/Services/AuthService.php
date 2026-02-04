<?php

declare(strict_types=1);

namespace Src\Domain\Auth\Services;

class AuthService implements AuthServiceInterface
{
    public function hashPassword(string $password): string
    {
