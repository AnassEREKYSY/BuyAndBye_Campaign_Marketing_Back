<?php

declare(strict_types=1);

namespace Src\Application\Auth\UseCases;

use Src\Application\Auth\DTOs\AuthResponse;
use Src\Application\Auth\DTOs\RegisterRequest;
use Src\Domain\Auth\Services\AuthServiceInterface;
use Src\Domain\Users\Exceptions\UserAlreadyExistsException;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Shared\Enums\AccountStatus;
use Src\Domain\Shared\Enums\UserRole;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private AuthServiceInterface $authService
    ) {
    }

    public function execute(RegisterRequest $request): AuthResponse
    {
        if ($this->users->findByEmail($request->email)) {
            throw UserAlreadyExistsException::forEmail($request->email);
        }

        $user = $this->users->create([
            'email' => $request->email,
            'password' => $this->authService->hashPassword($request->password),
            'display_name' => $request->displayName,
            'photo_url' => $request->photoUrl,
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Incomplete->value,
        ]);

        $token = $this->authService->createTokenForUserId($user->id);
        $expiresIn = $this->authService->getTokenExpirationMinutes();

        return new AuthResponse(
            token: $token,
            expiresIn: $expiresIn,
            userId: $user->id,
            profileStatus: $user->status,
            isProfileComplete: $user->profileCompletedAt !== null
        );
    }
}
