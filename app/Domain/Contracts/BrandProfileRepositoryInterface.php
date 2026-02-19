<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\BrandProfile;
use App\Models\User;

interface BrandProfileRepositoryInterface
{
    public function upsertForUser(User $user, array $data): BrandProfile;
}