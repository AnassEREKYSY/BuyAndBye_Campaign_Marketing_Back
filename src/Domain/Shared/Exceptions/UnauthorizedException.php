<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Exceptions;

use RuntimeException;

class UnauthorizedException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Unauthorized.');
    }
}
