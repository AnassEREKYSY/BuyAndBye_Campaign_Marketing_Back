<?php

declare(strict_types=1);

namespace Src\Api\V1\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Application\Users\DTOs\GetUserProfileResponse;

class ProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        /** @var GetUserProfileResponse $profile */
        $profile = $this->resource;

        return [
            'user' => (new UserResource($profile->user))->toArray($request),
            'preferences' => $profile->preferences,
            'businessInfo' => $profile->businessInfo,
            'paymentMethods' => $profile->paymentMethods,
        ];
    }
}
