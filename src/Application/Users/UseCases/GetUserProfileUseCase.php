<?php

declare(strict_types=1);

namespace Src\Application\Users\UseCases;

use Src\Application\Users\DTOs\GetUserProfileResponse;
use Src\Application\Users\Mappers\UserMapper;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Users\Exceptions\UserNotFoundException;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Users\Services\UserContextInterface;
use Src\Domain\SellerProfiles\Repositories\SellerProfileRepositoryInterface;

class GetUserProfileUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private SellerProfileRepositoryInterface $sellerProfiles,
        private UserContextInterface $userContext
    ) {
    }

    public function execute(): GetUserProfileResponse
    {
        $userId = $this->userContext->getUserId();
        $user = $this->users->findById($userId);

        if (!$user) {
            throw UserNotFoundException::forId($userId);
        }

        $preferences = [
            'categories' => $user->buyerCategories,
            'interests' => $user->buyerInterests,
        ];

        $businessInfo = null;
        if ($user->role === UserRole::Seller->value) {
            $profile = $this->sellerProfiles->findByUserId($userId);
            $businessInfo = $profile ? [
                'storeName' => $profile->storeName,
                'storeDescription' => $profile->storeDescription,
                'storeBannerUrl' => $profile->storeBannerUrl,
                'verificationStatus' => $profile->verificationStatus,
                'ratingAverage' => $profile->ratingAverage,
                'ratingCount' => $profile->ratingCount,
                'vatNumber' => $profile->vatNumber,
                'companyName' => $profile->companyName,
                'supportEmail' => $profile->supportEmail,
                'supportPhone' => $profile->supportPhone,
                'categoryTags' => $profile->categoryTags,
                'isProSeller' => $profile->isProSeller,
            ] : null;
        }

        return new GetUserProfileResponse(
            user: UserMapper::toUserResponse($user),
            preferences: $preferences,
            businessInfo: $businessInfo,
            paymentMethods: $user->paymentMethods
        );
    }
}
