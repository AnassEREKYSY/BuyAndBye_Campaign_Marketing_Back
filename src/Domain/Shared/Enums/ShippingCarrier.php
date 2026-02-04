<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum ShippingCarrier: string
{
    case DHL = 'DHL';
    case UPS = 'UPS';
    case FedEx = 'FedEx';
    case USPS = 'USPS';
    case Other = 'Other';
}
