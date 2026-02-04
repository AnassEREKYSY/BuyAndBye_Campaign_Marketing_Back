<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum AccountStatus: string
{
    case PendingVerification = 'PendingVerification';
    case Active = 'Active';
    case Suspended = 'Suspended';
    case Banned = 'Banned';
    case Deleted = 'Deleted';
    case Incomplete = 'Incomplete';
    case Skipped = 'Skipped';
}
