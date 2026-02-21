<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\TrackingLinkRepositoryInterface;
use App\Models\TrackingLink;

class TrackingLinkRepository implements TrackingLinkRepositoryInterface
{
    public function findByCode(string $code): ?TrackingLink
    {
        return TrackingLink::query()
            ->with(['collaboration.campaign.product', 'collaboration.influencer'])
            ->where('code', $code)
            ->first();
    }

    public function findByCollaborationId(string $collaborationId): ?TrackingLink
    {
        return TrackingLink::query()->where('collaboration_id', $collaborationId)->first();
    }

    public function create(array $data): TrackingLink
    {
        return TrackingLink::create($data);
    }
}