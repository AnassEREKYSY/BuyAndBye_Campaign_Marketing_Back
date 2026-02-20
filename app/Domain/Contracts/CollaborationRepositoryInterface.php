<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\Collaboration;

interface CollaborationRepositoryInterface
{
    public function findByCampaignAndInfluencer(string $campaignId, string $influencerId): ?Collaboration;

    public function create(array $data): Collaboration;
}