<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\User;
use App\Models\UserProfile;

interface UserProfileRepositoryInterface
{
    public function upsertForUser(User $user, array $data): UserProfile;
}
