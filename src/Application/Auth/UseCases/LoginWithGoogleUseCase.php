<?php

declare(strict_types=1);

namespace Src\Application\Auth\UseCases;

use DateTimeImmutable;
use Src\Application\Auth\DTOs\AuthResponse;
use Src\Application\Auth\DTOs\GoogleLoginRequest;
use Src\Domain\Auth\Services\AuthServiceInterface;
use Src\Domain\Auth\Services\GoogleAuthServiceInterface;
use Src\Domain\Shared\Enums\AccountStatus;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Users\Repositories\UserRepositoryInterface;

class LoginWithGoogleUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private AuthServiceInterface $authService,
        private GoogleAuthServiceInterface $googleAuth
    ) {
    }

    public function execute(GoogleLoginRequest $request): AuthResponse
    {
        $googleUser = $this->googleAuth->verifyIdToken($request->idToken);
        $user = $this->users->findByEmail($googleUser['email']);

        if ($user) {
            $user = $this->users->update($user->id, [
                'last_login_at' => new DateTimeImmutable(),
                'photo_url' => $user->photoUrl ?? $googleUser['photo_url'],
                'is_email_verified' => true,
            ]);
        } else {
            $displayName = $googleUser['name'] ?? strstr($googleUser['email'], '@', true) ?: $googleUser['email'];
            $user = $this->users->create([
                'email' => $googleUser['email'],
                'password' => null,
                'display_name' => $displayName,
                'photo_url' => $googleUser['photo_url'],
                'role' => UserRole::Buyer->value,
                'status' => AccountStatus::Incomplete->value,
                'is_email_verified' => true,
                'last_login_at' => new DateTimeImmutable(),
            ]);
        }

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
