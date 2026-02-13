<?php

declare(strict_types=1);

namespace App\Application\Dtos\Profile;

final class BecomeSellerDTO
{
    public function __construct(
        public readonly string $storeName,
        public readonly string $countryCode,
    ) {}
}
