<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Buyer = 'buyer';
    case Seller = 'seller';
    case Admin = 'admin';

    public function isSeller(): bool
    {
        return $this === self::Seller;
    }

    public function isSellerOrAdmin(): bool
    {
        return $this === self::Seller || $this === self::Admin;
    }

    public function isBuyer(): bool
    {
        return $this === self::Buyer;
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
