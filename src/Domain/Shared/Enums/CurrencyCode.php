<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum CurrencyCode: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
}
