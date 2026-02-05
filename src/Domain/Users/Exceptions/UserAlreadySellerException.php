<?php

declare(strict_types=1);

namespace Src\Domain\Users\Exceptions;

class UserAlreadySellerException extends UserException
{
    public static function forUser(string $userId): self
    {
        return new self("User with ID {$userId} is already a seller");
    }
}
