<?php

declare(strict_types=1);

namespace Src\Application\Auth\UseCases;

use DateTimeImmutable;
use Src\Application\Auth\DTOs\AuthResponse;
use Src\Application\Auth\DTOs\LoginRequest;
use Src\Domain\Auth\Exceptions\InvalidCredentialsException;
use Src\Domain\Auth\Services\AuthServiceInterface;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Shared\Enums\AccountStatus;

final class LoginUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private AuthServiceInterface $authService
    ) {
    }

    public function execute(LoginRequest $request): AuthResponse
    {
        $user = $this->users->findByEmail($request->email);

        if (!$user || !$user->password) {
            throw InvalidCredentialsException::create();
        }

        if (!$this->authService->verifyPassword($request->password, $user->password)) {
            throw InvalidCredentialsException::create();
        }

        $user = $this->users->update($user->id, [
            'last_login_at' => new DateTimeImmutable(),
        ]);

        $token = $this->authService->createTokenForUserId($user->id);
        $expiresIn = $this->authService->getTokenExpirationMinutes();

        return new AuthResponse(
            token: $token,
            expiresIn: $expiresIn,
            userId: $user->id,
            profileStatus: $user->status->value,
            isProfileComplete: $user->status === AccountStatus::Active
        );
    }
}
