<?php

declare(strict_types=1);

namespace Src\Domain\SellerProfiles\Repositories;

use Src\Domain\SellerProfiles\Entities\SellerProfile;

interface SellerProfileRepositoryInterface
{
    public function findByUserId(string $userId): ?SellerProfile;

    public function create(array $attributes): SellerProfile;

    public function updateByUserId(string $userId, array $attributes): SellerProfile;
}
