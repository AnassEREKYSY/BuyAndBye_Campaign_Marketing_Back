<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\UserProfileRepositoryInterface;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    public function upsertForUser(User $user, array $data): UserProfile
    {
        return UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, ['user_id' => $user->id])
        );
    }
}
