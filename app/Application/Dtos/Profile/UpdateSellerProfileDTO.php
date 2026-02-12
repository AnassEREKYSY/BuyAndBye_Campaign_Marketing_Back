<?php

declare(strict_types=1);

namespace App\Application\Dtos\Profile;
use Illuminate\Http\UploadedFile;

class UpdateSellerProfileDTO
{
    public function __construct(
        public readonly ?string $storeName,
        public readonly ?string $companyName,
        public readonly ?string $vatNumber,
        public readonly ?string $supportEmail,
        public readonly ?string $supportPhone,
        public readonly ?array $categoryTags,
        public readonly ?string $storeDescription,
        public readonly ?UploadedFile $storeBanner,
    ) {}
}
