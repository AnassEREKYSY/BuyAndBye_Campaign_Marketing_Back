<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum DiscountType: string
{
    case Percentage = 'Percentage';
    case FixedAmount = 'FixedAmount';
}
