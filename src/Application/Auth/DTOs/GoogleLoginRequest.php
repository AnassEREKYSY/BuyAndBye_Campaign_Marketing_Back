<?php

declare(strict_types=1);

namespace Src\Application\Auth\DTOs;

class GoogleLoginRequest
{
    public function __construct(public string $idToken)
    {
    }
}
