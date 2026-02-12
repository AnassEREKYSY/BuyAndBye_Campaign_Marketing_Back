<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\UpdateUserProfileDTO;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\UserProfileRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Models\User;

class UpdateUserProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserProfileRepositoryInterface $profileRepository,
        private readonly FileStorageInterface $fileStorage,
    ) {}

    public function execute(User $user, UpdateUserProfileDTO $dto): void
    {
        $userUpdate = array_filter([
            'display_name' => $dto->displayName,
        ], fn ($v) => $v !== null);

        if ($dto->photo) {
            if ($user->photo_url) {
                $this->fileStorage->deleteByUrl($user->photo_url);
            }

            $userUpdate['photo_url'] = $this->fileStorage
                ->storeUserAvatar((string) $user->id, $dto->photo);
        }

        if (!empty($userUpdate)) {
            $this->userRepository->update($user, $userUpdate);
        }

        $profileUpdate = array_filter([
            'phone_number' => $dto->phoneNumber,
            'birth_date' => $dto->birthDate,
            'gender' => $dto->gender,
            'country_code' => $dto->countryCode,
            'locale' => $dto->locale,
            'buyer_categories' => $dto->buyerCategories,
            'buyer_interests' => $dto->buyerInterests,
            'payment_methods' => $dto->paymentMethods,
        ], fn ($v) => $v !== null);

        if (!empty($profileUpdate)) {
            $this->profileRepository->upsertForUser($user, $profileUpdate);
        }

        $this->updateAccountStatus($user);
    }

    private function updateAccountStatus(User $user): void
    {
        $user->load('profile');

        $profile = $user->profile;

        $isComplete =
            $user->display_name &&
            $profile?->phone_number &&
            $profile?->birth_date &&
            $profile?->gender &&
            $profile?->country_code &&
            $profile?->locale;

        $this->userRepository->update($user, [
            'status' => $isComplete
                ? AccountStatus::Active
                : AccountStatus::Incomplete
        ]);
    }
}
