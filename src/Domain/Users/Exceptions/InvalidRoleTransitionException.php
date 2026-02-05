<?php

declare(strict_types=1);

namespace Src\Domain\Users\Exceptions;

use Src\Domain\Shared\Enums\UserRole;

class InvalidRoleTransitionException extends UserException
{
    public static function fromRole(UserRole $currentRole): self
    {
        return new self("Cannot become seller from role: {$currentRole->value}. Only buyers can transition to seller.");
    }
}
