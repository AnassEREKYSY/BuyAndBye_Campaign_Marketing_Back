<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\BrandProfileRepositoryInterface;
use App\Models\BrandProfile;
use App\Models\User;

class BrandProfileRepository implements BrandProfileRepositoryInterface
{
    public function upsertForUser(User $user, array $data): BrandProfile
    {
        return BrandProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, ['user_id' => $user->id])
        );
    }
}