<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\CompleteProfileDTO;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Models\User;

class CompleteProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $user, CompleteProfileDTO $dto): void
    {
        $this->userRepository->update($user, [
            'display_name' => $dto->displayName,
            'photo_url' => $dto->photoUrl ?? $user->photo_url,
            'profile_completed' => true,
            'profile_skipped' => false,
            'status' => AccountStatus::Active->value,
        ]);
    }
}
