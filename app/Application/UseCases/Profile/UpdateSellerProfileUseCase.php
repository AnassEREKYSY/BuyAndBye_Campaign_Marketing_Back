<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\UpdateSellerProfileDTO;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\SellerProfileRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Models\User;

class UpdateSellerProfileUseCase
{
    public function __construct(
        private readonly SellerProfileRepositoryInterface $sellerProfileRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly FileStorageInterface $fileStorage,
    ) {}

    public function execute(User $user, UpdateSellerProfileDTO $dto): void
    {
        $update = array_filter([
            'store_name' => $dto->storeName,
            'company_name' => $dto->companyName,
            'vat_number' => $dto->vatNumber,
            'support_email' => $dto->supportEmail,
            'support_phone' => $dto->supportPhone,
            'category_tags' => $dto->categoryTags,
            'store_description' => $dto->storeDescription,
        ], fn ($v) => $v !== null);

        if ($dto->storeBanner) {
            $update['store_banner_url'] =
                $this->fileStorage->storeStoreBanner((string) $user->id, $dto->storeBanner);
        }

        if (!empty($update)) {
            $this->sellerProfileRepository->upsertForUser($user, $update);
        }

        $this->updateAccountStatus($user);
    }

    private function updateAccountStatus(User $user): void
    {
        $user->load('sellerProfile');

        $profile = $user->sellerProfile;

        $isComplete =
            $user->display_name &&
            $profile?->store_name &&
            $profile?->company_name &&
            $profile?->vat_number &&
            $profile?->support_email &&
            $profile?->support_phone &&
            $profile?->store_description;

        $this->userRepository->update($user, [
            'status' => $isComplete
                ? AccountStatus::Active
                : AccountStatus::Incomplete
        ]);
    }
}
