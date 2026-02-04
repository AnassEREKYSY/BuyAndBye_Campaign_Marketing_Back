<?php

declare(strict_types=1);

namespace Src\Application\Users\UseCases;

use Src\Domain\Shared\Enums\AccountStatus;
use Src\Domain\Users\Exceptions\UserNotFoundException;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Users\Services\UserContextInterface;

class SkipProfileUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private UserContextInterface $userContext
    ) {
    }

    public function execute(): void
    {
        $userId = $this->userContext->getUserId();
        $user = $this->users->findById($userId);

        if (!$user) {
            throw UserNotFoundException::forId($userId);
        }

        $this->users->update($userId, [
            'profile_skipped' => true,
            'status' => AccountStatus::Skipped->value,
        ]);
    }
}
