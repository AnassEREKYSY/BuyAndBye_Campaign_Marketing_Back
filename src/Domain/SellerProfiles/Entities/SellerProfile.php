<?php

declare(strict_types=1);

namespace Src\Domain\SellerProfiles\Entities;

class SellerProfile
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $storeName,
        public ?string $storeDescription = null,
        public ?string $storeBannerUrl = null,
        public string $verificationStatus = 'NotSubmitted',
        public float $ratingAverage = 0.0,
        public int $ratingCount = 0,
        public ?string $vatNumber = null,
        public ?string $companyName = null,
        public ?string $supportEmail = null,
        public ?string $supportPhone = null,
        public ?array $categoryTags = null,
        public bool $isProSeller = false
    ) {
    }
}
