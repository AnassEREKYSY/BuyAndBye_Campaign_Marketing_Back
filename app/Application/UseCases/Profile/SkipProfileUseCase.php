<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Models\User;

class SkipProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $user): void
    {
        $this->userRepository->update($user, [
            'profile_skipped' => true,
            'status' => AccountStatus::Skipped->value,
        ]);
    }
}
