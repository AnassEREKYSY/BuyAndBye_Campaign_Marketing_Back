<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum AuthErrorCode: string
{
    case InvalidCredentials = 'InvalidCredentials';
    case AccountDisabled = 'AccountDisabled';
    case TokenExpired = 'TokenExpired';
    case TokenInvalid = 'TokenInvalid';
}
