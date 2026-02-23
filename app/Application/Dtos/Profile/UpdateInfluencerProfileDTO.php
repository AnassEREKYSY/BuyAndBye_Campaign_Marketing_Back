<?php

declare(strict_types=1);

namespace App\Application\Dtos\Profile;

use Illuminate\Http\UploadedFile;

final class UpdateInfluencerProfileDTO
{
    public function __construct(
        public readonly ?string $niche,
        public readonly ?string $instagramUrl,
        public readonly ?string $tiktokUrl,
        public readonly ?string $youtubeUrl,
        public readonly ?int $followersInstagram,
        public readonly ?int $followersTiktok,
        public readonly ?int $followersYoutube,
        public readonly ?float $avgEngagementRate,
        public readonly ?string $countryCode,
        public readonly ?string $language,
        public readonly ?string $mediaKitUrl,
        public readonly ?UploadedFile $photo,
    ) {}
}