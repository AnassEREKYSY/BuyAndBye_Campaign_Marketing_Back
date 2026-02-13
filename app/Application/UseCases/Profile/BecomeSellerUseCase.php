<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\BecomeSellerDTO;
use App\Domain\Contracts\SellerProfileRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Exceptions\InvalidRoleTransitionException;
use App\Exceptions\UserAlreadySellerException;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BecomeSellerUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SellerProfileRepositoryInterface $sellerProfileRepository,
    ) {}

    public function execute(User $user, BecomeSellerDTO $dto): void
    {
        if ($user->role === UserRole::Seller) {
            throw UserAlreadySellerException::forUser($user->id);
        }

        if ($user->role !== UserRole::Buyer) {
            throw InvalidRoleTransitionException::fromRole($user->role);
        }

        DB::transaction(function () use ($user, $dto): void {
            $this->userRepository->update($user, [
                'role' => UserRole::Seller,
                'status' => AccountStatus::Incomplete,
                'profile_completed' => false,
                'profile_skipped' => false,
            ]);

            $this->sellerProfileRepository->upsertForUser($user, [
                'store_name' => $dto->storeName,
            ]);
        });
    }
}
