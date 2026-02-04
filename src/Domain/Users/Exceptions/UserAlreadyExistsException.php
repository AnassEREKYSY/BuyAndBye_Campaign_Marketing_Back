<?php

declare(strict_types=1);

namespace Src\Domain\Users\Exceptions;

use RuntimeException;

class UserAlreadyExistsException extends RuntimeException
{
    public static function forEmail(string $email): self
    {
        return new self("User with email {$email} already exists.");
    }
}
