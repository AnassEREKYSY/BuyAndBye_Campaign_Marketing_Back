<?php

declare(strict_types=1);

namespace App\Application\Dtos\Profile;
use Illuminate\Http\UploadedFile;

class UpdateUserProfileDTO
{
    public function __construct(
        public readonly ?string $displayName,
        public readonly ?UploadedFile $photo,
        public readonly ?string $phoneNumber,
        public readonly ?string $birthDate,
        public readonly ?string $gender,
        public readonly ?string $countryCode,
        public readonly ?string $locale,
        public readonly ?array $buyerCategories,
        public readonly ?array $buyerInterests,
        public readonly ?array $paymentMethods,
    ) {}
}
