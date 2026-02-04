<?php

declare(strict_types=1);

namespace Src\Application\Auth\DTOs;

class LoginRequest
{
    public function __construct(
        public string $email,
        public string $password
    ) 
    {
    }
}
