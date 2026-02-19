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
        public string $accountStatus,
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
                'accountStatus' => $this->accountStatus,
                'isProfileComplete' => $this->isProfileComplete,
            ],
        ];
    }

    public static function fromUser($user, string $token): self
    {
        $isComplete = (bool) $user->profile_completed && $user->status === AccountStatus::Active;

        return new self(
            token: $token,
            expiresIn: config('sanctum.expiration'),
            userId: $user->id,
            accountStatus: $user->status->value,
            isProfileComplete: $isComplete
        );
    }
}