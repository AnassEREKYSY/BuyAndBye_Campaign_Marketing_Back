<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\InfluencerProfileRepositoryInterface;
use App\Models\InfluencerProfile;
use App\Models\User;

class InfluencerProfileRepository implements InfluencerProfileRepositoryInterface
{
    public function upsertForUser(User $user, array $data): InfluencerProfile
    {
        return InfluencerProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, ['user_id' => $user->id])
        );
    }

    public function findByUserId(string $userId): ?InfluencerProfile
    {
        return InfluencerProfile::query()
            ->with(['user'])
            ->where('user_id', $userId)
            ->first();
    }
}