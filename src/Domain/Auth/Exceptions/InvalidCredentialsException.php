<?php

declare(strict_types=1);

namespace Src\Domain\Auth\Exceptions;

use RuntimeException;

class InvalidCredentialsException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Invalid credentials.');
    }
}
