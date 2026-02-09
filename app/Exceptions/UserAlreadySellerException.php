<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserAlreadySellerException extends HttpException
{
    public function __construct(string $userId)
    {
        parent::__construct(400, "User with ID {$userId} is already a seller.");
    }

    public static function forUser(string $userId): self
    {
        return new self($userId);
    }
}
