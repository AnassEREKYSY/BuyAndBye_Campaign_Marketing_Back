<?php

declare(strict_types=1);

namespace Src\Application\Users\DTOs;

class CompleteProfileRequest
{
    public function __construct(
        public string $displayName,
        public ?string $phoneNumber,
        public ?string $birthDate,
        public ?string $gender,
        public ?string $countryCode,
        public ?string $locale,
        public ?string $photoUrl,
        public ?array $buyerCategories,
        public ?array $buyerInterests,
        public ?array $paymentMethods,
        public ?string $storeName,
        public ?string $companyName,
        public ?string $vatNumber,
        public ?string $supportEmail,
        public ?string $supportPhone,
        public ?array $categoryTags,
        public ?string $storeDescription,
        public ?string $storeBannerUrl
    ) {
    }
}
