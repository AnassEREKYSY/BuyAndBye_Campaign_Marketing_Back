<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\BecomeSellerDTO;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Exceptions\InvalidRoleTransitionException;
use App\Exceptions\UserAlreadySellerException;
use App\Models\User;

class BecomeSellerUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $user, BecomeSellerDTO $dto): void
    {
        if ($user->role === UserRole::Seller) {
            throw UserAlreadySellerException::forUser($user->id);
        }

        if ($user->role !== UserRole::Buyer) {
            throw InvalidRoleTransitionException::fromRole($user->role);
        }

        $this->userRepository->update($user, [
            'role' => UserRole::Seller->value,
            'status' => AccountStatus::Incomplete->value,
            'profile_completed' => false,
            'profile_skipped' => false,
        ]);
    }
}
