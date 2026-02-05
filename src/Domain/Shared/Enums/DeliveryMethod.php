<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum DeliveryMethod: string
{
    case Shipping = 'Shipping';
    case Pickup = 'Pickup';
    case Digital = 'Digital';
}
