<?php

declare(strict_types=1);

namespace Src\Application\Users\Mappers;

use Src\Application\Users\DTOs\UserResponse;
use Src\Domain\Users\Entities\User;

class UserMapper
{
    public static function toUserResponse(User $user): UserResponse
    {
            return new UserResponse(
            id: $user->id,
            email: $user->email,
            displayName: $user->displayName ?? '',
            role: $user->role,
            status: $user->status->value,
            photoUrl: $user->photoUrl,
            phoneNumber: null,
            birthDate: null,
            gender: null,

            isEmailVerified: $user->emailVerifiedAt !== null,
            isPhoneVerified: false,

            locale: 'en',
            countryCode: null
        );
    }
}
