<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum PaymentStatus: string
{
    case Pending = 'Pending';
    case Authorized = 'Authorized';
    case Captured = 'Captured';
    case Failed = 'Failed';
    case Refunded = 'Refunded';
}
