<?php

declare(strict_types=1);

namespace Src\Application\Users\DTOs;

class UpdateProfileRequest
{
    public function __construct(
        public ?string $displayName = null,
        public ?string $phoneNumber = null,
        public ?string $birthDate = null,
        public ?string $gender = null,
        public ?string $countryCode = null,
        public ?string $locale = null,
        public ?string $photoUrl = null,
        public ?array $buyerCategories = null,
        public ?array $buyerInterests = null,
        public ?array $paymentMethods = null,
        public ?string $storeName = null,
        public ?string $companyName = null,
        public ?string $vatNumber = null,
        public ?string $supportEmail = null,
        public ?string $supportPhone = null,
        public ?array $categoryTags = null,
        public ?string $storeDescription = null,
        public ?string $storeBannerUrl = null
    ) {
    }
}
