<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\UpdateSellerProfileDTO;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\SellerProfileRepositoryInterface;
use App\Models\User;

class UpdateSellerProfileUseCase
{
    public function __construct(
        private readonly SellerProfileRepositoryInterface $sellerProfileRepository,
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
            $update['store_banner_url'] = $this->fileStorage->storeStoreBanner((string) $user->id, $dto->storeBanner);
        }

        if (!empty($update)) {
            $this->sellerProfileRepository->upsertForUser($user, $update);
        }
    }
}
