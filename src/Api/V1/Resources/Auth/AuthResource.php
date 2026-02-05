<?php

declare(strict_types=1);

namespace Src\Api\V1\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Application\Auth\DTOs\AuthResponse;

final class AuthResource extends JsonResource
{
    public function __construct(AuthResponse $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        /** @var AuthResponse $auth */
        $auth = $this->resource;

        return [
            'success' => true,
            'message' => 'Authenticated successfully',
            'data' => [
                'token' => $auth->token,
                'expiresIn' => $auth->expiresIn,
                'userId' => $auth->userId,
                'profileStatus' => $auth->profileStatus,
                'isProfileComplete' => $auth->isProfileComplete,
            ],
        ];
    }
}
