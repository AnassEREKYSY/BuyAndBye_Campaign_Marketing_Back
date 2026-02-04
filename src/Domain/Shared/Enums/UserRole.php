<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum UserRole: int
{
    case Buyer = 1;
    case Seller = 2;
    case Admin = 3;
}
