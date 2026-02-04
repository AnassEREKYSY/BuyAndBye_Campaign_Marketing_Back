<?php

declare(strict_types=1);

namespace Src\Application\Auth\UseCases;

use Src\Application\Users\DTOs\UserResponse;
use Src\Application\Users\Mappers\UserMapper;
use Src\Domain\Users\Exceptions\UserNotFoundException;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Users\Services\UserContextInterface;

class GetAuthenticatedUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private UserContextInterface $userContext
    ) {
    }

    public function execute(): UserResponse
    {
        $userId = $this->userContext->getUserId();
        $user = $this->users->findById($userId);

        if (!$user) {
            throw UserNotFoundException::forId($userId);
        }

        return UserMapper::toUserResponse($user);
    }
}
