<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\Collaboration;

class CollaborationRepository implements CollaborationRepositoryInterface
{
    public function findByCampaignAndInfluencer(string $campaignId, string $influencerId): ?Collaboration
    {
        return Collaboration::query()
            ->where('campaign_id', $campaignId)
            ->where('influencer_id', $influencerId)
            ->first();
    }

    public function create(array $data): Collaboration
    {
        return Collaboration::create($data);
    }
}