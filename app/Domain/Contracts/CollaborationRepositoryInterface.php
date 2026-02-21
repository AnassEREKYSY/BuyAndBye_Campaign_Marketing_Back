<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\Collaboration;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CollaborationRepositoryInterface
{
    public function findByCampaignAndInfluencer(string $campaignId, string $influencerId): ?Collaboration;

    public function create(array $data): Collaboration;

    public function findById(string $id): ?Collaboration;

    public function paginateForBrand(User $brand, int $page, int $size): LengthAwarePaginator;

    public function paginateForInfluencer(User $influencer, int $page, int $size): LengthAwarePaginator;
}