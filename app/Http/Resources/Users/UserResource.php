<?php

declare(strict_types=1);

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Application\Users\DTOs\UserResponse;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        /** @var UserResponse $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'email' => $user->email,
            'displayName' => $user->displayName,
            'role' => $user->role,
            'status' => $user->status,
            'photoUrl' => $user->photoUrl,
            'phoneNumber' => $user->phoneNumber,
            'birthDate' => $user->birthDate,
            'gender' => $user->gender,
            'isEmailVerified' => $user->isEmailVerified,
            'isPhoneVerified' => $user->isPhoneVerified,
            'locale' => $user->locale,
            'countryCode' => $user->countryCode,
        ];
    }
}
