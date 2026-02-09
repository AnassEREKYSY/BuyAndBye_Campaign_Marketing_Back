<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidCredentialsException extends HttpException
{
    public function __construct()
    {
        parent::__construct(401, 'Invalid credentials.');
    }

    public static function create(): self
    {
        return new self;
    }
}
