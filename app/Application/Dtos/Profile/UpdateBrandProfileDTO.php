<?php

declare(strict_types=1);

namespace App\Application\Dtos\Profile;

use Illuminate\Http\UploadedFile;

final class UpdateBrandProfileDTO
{
    public function __construct(
        public readonly ?string $brandName,
        public readonly ?string $websiteUrl,
        public readonly ?string $industry,
        public readonly ?string $contactEmail,
        public readonly ?string $contactPhone,
        public readonly ?string $description,
        public readonly ?UploadedFile $logo,
    ) {}
}