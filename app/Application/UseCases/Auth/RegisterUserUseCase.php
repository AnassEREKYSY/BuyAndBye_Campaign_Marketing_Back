<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth;

use App\Application\Dtos\Auth\RegisterUserDTO;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Contracts\UserProfileRepositoryInterface;
use App\Domain\Contracts\SellerProfileRepositoryInterface;
use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserProfileRepositoryInterface $userProfileRepository,
        private readonly SellerProfileRepositoryInterface $sellerProfileRepository,
        private readonly FileStorageInterface $fileStorage,
    ) {}

    public function execute(RegisterUserDTO $dto): array
    {
        if ($this->userRepository->findByEmail($dto->email)) {
            throw UserAlreadyExistsException::forEmail($dto->email);
        }

        return DB::transaction(function () use ($dto) {

            $user = new User();
            $user->email = $dto->email;
            $user->password = Hash::make($dto->password);
            $user->display_name = $dto->displayName;
            $user->role = UserRole::Buyer->value;
            $user->status = AccountStatus::Incomplete->value;

            $this->userRepository->save($user);

            if ($dto->photo) {
                $photoUrl = $this->fileStorage->storeUserAvatar((string)$user->id, $dto->photo);
                $this->userRepository->update($user, ['photo_url' => $photoUrl]);
            }

            $this->userProfileRepository->upsertForUser($user, []);

            $token = $user->createToken('api-token')->plainTextToken;

            return [$user->fresh(['profile', 'sellerProfile']), $token];
        });
    }
}
