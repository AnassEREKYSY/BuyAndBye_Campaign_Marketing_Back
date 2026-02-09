<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserAlreadyExistsException extends HttpException
{
    public function __construct(string $email)
    {
        parent::__construct(409, "User with email {$email} already exists.");
    }

    public static function forEmail(string $email): self
    {
        return new self($email);
    }
}
