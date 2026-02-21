<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Models\CampaignPayoutTier;
use Illuminate\Support\Collection;

class CampaignPayoutTierRepository implements CampaignPayoutTierRepositoryInterface
{
    public function listForCampaign(string $campaignId): Collection
    {
        return CampaignPayoutTier::query()
            ->where('campaign_id', $campaignId)
            ->orderBy('from_value')
            ->get();
    }

    public function findById(string $id): ?CampaignPayoutTier
    {
        return CampaignPayoutTier::query()->with('campaign')->where('id', $id)->first();
    }

    public function create(array $data): CampaignPayoutTier
    {
        return CampaignPayoutTier::create($data);
    }

    public function update(CampaignPayoutTier $tier, array $data): void
    {
        $tier->update($data);
    }

    public function delete(CampaignPayoutTier $tier): void
    {
        $tier->delete();
    }
}