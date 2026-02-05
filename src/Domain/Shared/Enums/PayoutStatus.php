<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum PayoutStatus: string
{
    case Pending = 'Pending';
    case InTransit = 'InTransit';
    case Paid = 'Paid';
    case Failed = 'Failed';
}
