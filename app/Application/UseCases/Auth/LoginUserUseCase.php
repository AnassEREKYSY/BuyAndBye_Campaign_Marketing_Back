<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth;

use App\Application\Dtos\Auth\LoginUserDTO;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;

class LoginUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(LoginUserDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw InvalidCredentialsException::create();
        }

        if (in_array($user->status, [AccountStatus::Suspended, AccountStatus::Banned, AccountStatus::Deleted], true)) {
            throw new ForbiddenHttpException('Account is not allowed to login.');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [$user, $token];
    }
}