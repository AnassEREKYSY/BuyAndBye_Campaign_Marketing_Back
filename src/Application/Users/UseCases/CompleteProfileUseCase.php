<?php

declare(strict_types=1);

namespace Src\Application\Users\UseCases;

use DateTimeImmutable;
use Src\Application\Users\DTOs\CompleteProfileRequest;
use Src\Domain\Shared\Enums\AccountStatus;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Shared\Services\TransactionManagerInterface;
use Src\Domain\Users\Exceptions\UserNotFoundException;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Users\Services\UserContextInterface;
use Src\Domain\SellerProfiles\Repositories\SellerProfileRepositoryInterface;

class CompleteProfileUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private SellerProfileRepositoryInterface $sellerProfiles,
        private UserContextInterface $userContext,
        private TransactionManagerInterface $transactions
    ) {
    }

    public function execute(CompleteProfileRequest $request): void
    {
        $userId = $this->userContext->getUserId();
        $user = $this->users->findById($userId);

        if (!$user) {
            throw UserNotFoundException::forId($userId);
        }

        $this->transactions->run(function () use ($user, $request, $userId): void {
            $this->users->update($userId, [
                'display_name' => $request->displayName,
                'phone_number' => $request->phoneNumber,
                'birth_date' => $request->birthDate,
                'gender' => $request->gender,
                'country_code' => $request->countryCode,
                'locale' => $request->locale,
                'photo_url' => $request->photoUrl,
                'buyer_categories' => $request->buyerCategories,
                'buyer_interests' => $request->buyerInterests,
                'payment_methods' => $request->paymentMethods,
                'profile_completed_at' => new DateTimeImmutable(),
                'profile_skipped' => false,
                'status' => AccountStatus::Active->value,
            ]);

            if ($user->role === UserRole::Seller->value) {
                $attributes = [
                    'user_id' => $userId,
                    'store_name' => $request->storeName,
                    'company_name' => $request->companyName,
                    'vat_number' => $request->vatNumber,
                    'support_email' => $request->supportEmail,
                    'support_phone' => $request->supportPhone,
                    'category_tags' => $request->categoryTags,
                    'store_description' => $request->storeDescription,
                    'store_banner_url' => $request->storeBannerUrl,
                ];

                $existing = $this->sellerProfiles->findByUserId($userId);
                if ($existing) {
                    $this->sellerProfiles->updateByUserId($userId, $attributes);
                } else {
                    $this->sellerProfiles->create($attributes);
                }
            }
        });
    }
}
