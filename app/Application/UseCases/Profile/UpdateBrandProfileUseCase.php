<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\UpdateBrandProfileDTO;
use App\Domain\Contracts\BrandProfileRepositoryInterface;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateBrandProfileUseCase
{
    public function __construct(
        private readonly BrandProfileRepositoryInterface $brandProfiles,
        private readonly UserRepositoryInterface $users,
        private readonly FileStorageInterface $fileStorage,
    ) {}

    public function execute(User $user, UpdateBrandProfileDTO $dto): void
    {
        DB::transaction(function () use ($user, $dto): void {

            $update = array_filter([
                'brand_name' => $dto->brandName,
                'website_url' => $dto->websiteUrl,
                'industry' => $dto->industry,
                'contact_email' => $dto->contactEmail,
                'contact_phone' => $dto->contactPhone,
                'description' => $dto->description,
            ], fn($v) => $v !== null);

            if ($dto->logo) {
                $logoUrl = $this->fileStorage->storeUserAvatar((string)$user->id, $dto->logo);
                $update['logo_url'] = $logoUrl;
            }

            if (!empty($update)) {
                $this->brandProfiles->upsertForUser($user, $update);
            }

            $user->load('brandProfile');
            $p = $user->brandProfile;

            $isComplete = $user->display_name && $p?->brand_name && $p?->contact_email;
            $this->users->update($user, ['profile_completed' => (bool) $isComplete]);
        });
    }
}