<?php

declare(strict_types=1);

namespace Src\Application\Users\UseCases;

use Src\Application\Users\DTOs\UserProfileStatusResponse;
use Src\Domain\Users\Exceptions\UserNotFoundException;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Users\Services\UserContextInterface;

class GetProfileStatusUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private UserContextInterface $userContext
    ) {
    }

    public function execute(): UserProfileStatusResponse
    {
        $userId = $this->userContext->getUserId();
        $user = $this->users->findById($userId);

        if (!$user) {
            throw UserNotFoundException::forId($userId);
        }

        return new UserProfileStatusResponse(
            userId: $user->id,
            status: $user->status,
            isProfileComplete: $user->profileCompletedAt !== null
        );
    }
}
