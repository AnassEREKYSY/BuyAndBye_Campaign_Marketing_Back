<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth;

use App\Application\Dtos\Auth\RegisterUserDTO;
use App\Domain\Contracts\BrandProfileRepositoryInterface;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\InfluencerProfileRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly BrandProfileRepositoryInterface $brandProfileRepository,
        private readonly InfluencerProfileRepositoryInterface $influencerProfileRepository,
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
            $user->role = $dto->role;
            $user->status = AccountStatus::PendingVerification;
            $user->profile_completed = false;

            $this->userRepository->save($user);

            if ($dto->photo) {
                $photoUrl = $this->fileStorage->storeUserAvatar((string)$user->id, $dto->photo);
                $this->userRepository->update($user, ['photo_url' => $photoUrl]);
            }

            // Create empty profile based on role
            if ($user->role->isBrand()) {
                $this->brandProfileRepository->upsertForUser($user, []);
            } else {
                $this->influencerProfileRepository->upsertForUser($user, []);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return [$user->fresh(['brandProfile', 'influencerProfile']), $token];
        });
    }
}