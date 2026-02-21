<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\CollaborationPayoutRepositoryInterface;
use App\Enums\PayoutStatus;
use App\Models\CollaborationPayout;
use Illuminate\Support\Collection;

class CollaborationPayoutRepository implements CollaborationPayoutRepositoryInterface
{
    public function create(array $data): CollaborationPayout
    {
        return CollaborationPayout::create($data);
    }

    public function findById(string $id): ?CollaborationPayout
    {
        return CollaborationPayout::query()
            ->with(['collaboration.campaign', 'collaboration.brand', 'collaboration.influencer', 'tier'])
            ->where('id', $id)
            ->first();
    }

    public function updateStatus(CollaborationPayout $payout, PayoutStatus $status): void
    {
        $payout->update(['status' => $status->value]);
    }

    public function listForInfluencer(string $influencerId): Collection
    {
        return CollaborationPayout::query()
            ->with(['collaboration.campaign', 'collaboration.brand', 'tier'])
            ->whereHas('collaboration', fn ($q) => $q->where('influencer_id', $influencerId))
            ->latest()
            ->get();
    }

    public function existsForPeriod(string $collaborationId, string $start, string $end): bool
    {
        return CollaborationPayout::query()
            ->where('collaboration_id', $collaborationId)
            ->where('period_start', $start)
            ->where('period_end', $end)
            ->exists();
    }
}