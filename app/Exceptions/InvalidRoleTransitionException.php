<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\UserRole;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRoleTransitionException extends HttpException
{
    public function __construct(UserRole $currentRole)
    {
        parent::__construct(400, "Cannot become seller from role: {$currentRole->value}. Only buyers can transition to seller.");
    }

    public static function fromRole(UserRole $currentRole): self
    {
        return new self($currentRole);
    }
}
