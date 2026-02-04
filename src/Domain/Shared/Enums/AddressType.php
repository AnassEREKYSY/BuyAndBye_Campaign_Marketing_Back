<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum AddressType: string
{
    case Billing = 'Billing';
    case Shipping = 'Shipping';
}
