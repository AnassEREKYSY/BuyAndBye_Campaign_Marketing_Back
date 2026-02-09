<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\AccountStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function __construct(
        public string $token,
        public ?int $expiresIn,
        public string $userId,
        public string $profileStatus,
        public bool $isProfileComplete
    ) {
        parent::__construct(null);
    }

    public function toArray($request): array
    {
        return [
            'success' => true,
            'message' => 'Authenticated successfully',
            'data' => [
                'token' => $this->token,
                'expiresIn' => $this->expiresIn,
                'userId' => $this->userId,
                'profileStatus' => $this->profileStatus,
                'isProfileComplete' => $this->isProfileComplete,
            ],
        ];
    }

    public static function fromUser($user, string $token): self
    {
        return new self(
            token: $token,
            expiresIn: config('sanctum.expiration'),
            userId: $user->id,
            profileStatus: $user->status->value,
            isProfileComplete: $user->status === AccountStatus::Active
        );
    }
}
