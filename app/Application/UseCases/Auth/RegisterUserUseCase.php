<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth;

use App\Application\Dtos\Auth\RegisterUserDTO;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(RegisterUserDTO $dto): array
    {
        if ($this->userRepository->findByEmail($dto->email)) {
            throw UserAlreadyExistsException::forEmail($dto->email);
        }

        $user = new User();
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->display_name = $dto->displayName;
        $user->photo_url = $dto->photoUrl;
        $user->role = UserRole::Buyer->value;
        $user->status = AccountStatus::Incomplete->value;

        $this->userRepository->save($user);

        $token = $user->createToken('api-token')->plainTextToken;

        return [$user, $token];
    }
}
