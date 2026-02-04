<?php

declare(strict_types=1);

namespace Src\Api\V1\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Application\Auth\DTOs\AuthResponse;

class AuthResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        /** @var AuthResponse $auth */
        $auth = $this->resource;

        return [
            'token' => $auth->token,
            'expiresIn' => $auth->expiresIn,
            'userId' => $auth->userId,
            'profileStatus' => $auth->profileStatus,
            'isProfileComplete' => $auth->isProfileComplete,
        ];
    }
}
<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
}
