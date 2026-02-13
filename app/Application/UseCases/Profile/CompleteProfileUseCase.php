<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\CompleteProfileDTO;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CompleteProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $user, CompleteProfileDTO $dto): void
    {
        if ($user->profile_completed) {
            throw new ConflictHttpException('Profile is already completed.');
        }

        $this->userRepository->update($user, [
            'display_name' => $dto->displayName,
            'photo_url' => $dto->photoUrl ?? $user->photo_url,
            'profile_completed' => true,
            'profile_skipped' => false,
            'status' => AccountStatus::Active,
        ]);
    }
}
