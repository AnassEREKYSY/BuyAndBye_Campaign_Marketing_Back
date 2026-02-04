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
            displayName: $user->displayName,
            role: $user->role,
            status: $user->status,
            photoUrl: $user->photoUrl,
            phoneNumber: $user->phoneNumber,
            birthDate: $user->birthDate?->format('Y-m-d'),
            gender: $user->gender,
            isEmailVerified: $user->isEmailVerified,
            isPhoneVerified: $user->isPhoneVerified,
            locale: $user->locale,
            countryCode: $user->countryCode
        );
    }
}
