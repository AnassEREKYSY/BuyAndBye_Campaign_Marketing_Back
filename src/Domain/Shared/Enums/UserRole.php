<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum UserRole: string
{
    case Buyer = 'buyer';
    case Seller = 'seller';
    case Admin = 'admin';
}
