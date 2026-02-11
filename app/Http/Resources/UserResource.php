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
            'id' => $this->id,
            'email' => $this->email,
            'display_name' => $this->display_name,
            'photo_url' => $this->photo_url,
            'role' => $this->role,
            'status' => $this->status,
            'profile_completed' => $this->profile_completed,
            'profile_skipped' => $this->profile_skipped,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
