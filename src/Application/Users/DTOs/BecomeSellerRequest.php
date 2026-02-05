<?php

declare(strict_types=1);

namespace Src\Application\Users\DTOs;

class BecomeSellerRequest
{
    public function __construct(
        public string $storeName,
        public string $countryCode,
    ) {
    }
}
