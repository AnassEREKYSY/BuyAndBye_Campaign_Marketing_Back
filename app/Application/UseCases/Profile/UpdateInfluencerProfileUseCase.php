<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\UpdateInfluencerProfileDTO;
use App\Domain\Contracts\InfluencerProfileRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateInfluencerProfileUseCase
{
    public function __construct(
        private readonly InfluencerProfileRepositoryInterface $influencers,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function execute(User $user, UpdateInfluencerProfileDTO $dto): void
    {
        DB::transaction(function () use ($user, $dto): void {

            $update = array_filter([
                'niche' => $dto->niche,
                'instagram_url' => $dto->instagramUrl,
                'tiktok_url' => $dto->tiktokUrl,
                'youtube_url' => $dto->youtubeUrl,
                'followers_instagram' => $dto->followersInstagram,
                'followers_tiktok' => $dto->followersTiktok,
                'followers_youtube' => $dto->followersYoutube,
                'avg_engagement_rate' => $dto->avgEngagementRate,
                'country_code' => $dto->countryCode,
                'language' => $dto->language,
                'media_kit_url' => $dto->mediaKitUrl,
            ], fn($v) => $v !== null);

            if (!empty($update)) {
                $this->influencers->upsertForUser($user, $update);
            }

            $user->load('influencerProfile');
            $p = $user->influencerProfile;

            $isComplete = $user->display_name && ($p?->instagram_url || $p?->tiktok_url || $p?->youtube_url);
            $this->users->update($user, ['profile_completed' => (bool) $isComplete]);
        });
    }
}