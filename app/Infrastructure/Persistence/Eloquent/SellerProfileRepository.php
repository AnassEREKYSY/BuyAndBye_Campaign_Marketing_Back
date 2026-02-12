<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\SellerProfileRepositoryInterface;
use App\Models\SellerProfile;
use App\Models\User;

class SellerProfileRepository implements SellerProfileRepositoryInterface
{
    public function upsertForUser(User $user, array $data): SellerProfile
    {
        return SellerProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, ['user_id' => $user->id])
        );
    }
}
