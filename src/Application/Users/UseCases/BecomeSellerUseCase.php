<?php

declare(strict_types=1);

namespace Src\Application\Users\UseCases;

use Src\Application\Users\DTOs\BecomeSellerRequest;
use Src\Domain\Shared\Enums\AccountStatus;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Users\Exceptions\InvalidRoleTransitionException;
use Src\Domain\Users\Exceptions\UserAlreadySellerException;
use Src\Domain\Users\Exceptions\UserNotFoundException;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Users\Services\UserContextInterface;

class BecomeSellerUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private UserContextInterface $userContext
    ) {
    }

    public function execute(BecomeSellerRequest $request): void
    {
        $userId = $this->userContext->getUserId();
        $user = $this->users->findById($userId);

        if (!$user) {
            throw UserNotFoundException::forId($userId);
        }

        // Business rule: only buyers can become sellers
        if ($user->role === UserRole::Seller) {
            throw UserAlreadySellerException::forUser($userId);
        }

        if ($user->role !== UserRole::Buyer) {
            throw InvalidRoleTransitionException::fromRole($user->role);
        }

        // Update user to seller role and reset status to Incomplete
        $this->users->update($userId, [
            'role' => UserRole::Seller->value,
            'status' => AccountStatus::Incomplete->value,
            'profile_completed' => false,
            'profile_skipped' => false,
        ]);
    }
}
