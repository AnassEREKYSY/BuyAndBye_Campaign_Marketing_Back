<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'email' => $user->email,
            'displayName' => $user->display_name,
            'role' => $user->role->value,
            'status' => $user->status->value,
            'photoUrl' => $user->photo_url,
            'isEmailVerified' => $user->email_verified_at !== null,
        ];
    }
}
