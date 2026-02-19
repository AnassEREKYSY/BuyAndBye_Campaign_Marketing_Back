<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountStatus: string
{
    case PendingVerification = 'pending_verification';
    case Active = 'active';
    case Suspended = 'suspended';
    case Banned = 'banned';
    case Deleted = 'deleted';
}