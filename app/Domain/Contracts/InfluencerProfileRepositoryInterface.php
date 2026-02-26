<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\InfluencerProfile;
use App\Models\User;

interface InfluencerProfileRepositoryInterface
{
    public function upsertForUser(User $user, array $data): InfluencerProfile;

    public function findByUserId(string $userId): ?InfluencerProfile;
}