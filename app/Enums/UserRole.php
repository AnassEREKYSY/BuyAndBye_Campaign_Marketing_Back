<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Brand = 'brand';
    case Influencer = 'influencer';
    case Admin = 'admin';

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isBrand(): bool
    {
        return $this === self::Brand;
    }

    public function isInfluencer(): bool
    {
        return $this === self::Influencer;
    }
}