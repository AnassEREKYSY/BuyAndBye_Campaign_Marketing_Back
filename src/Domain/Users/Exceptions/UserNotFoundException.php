<?php

declare(strict_types=1);

namespace Src\Domain\Users\Exceptions;

use RuntimeException;

class UserNotFoundException extends RuntimeException
{
    public static function forId(string $id): self
    {
        return new self("User {$id} not found.");
    }
}
