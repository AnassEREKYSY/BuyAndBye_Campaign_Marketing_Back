<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum OrderStatus: string
{
    case Draft = 'Draft';
    case Placed = 'Placed';
    case Paid = 'Paid';
    case Shipped = 'Shipped';
    case Delivered = 'Delivered';
    case Cancelled = 'Cancelled';
    case Refunded = 'Refunded';
}
