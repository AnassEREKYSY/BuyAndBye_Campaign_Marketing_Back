<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CampaignApplicationRepository implements CampaignApplicationRepositoryInterface
{
    public function findById(string $id): ?CampaignApplication
    {
        return CampaignApplication::query()
            ->with(['campaign.product', 'influencer'])
            ->where('id', $id)
            ->first();
    }

    public function findByCampaignAndInfluencer(Campaign $campaign, User $influencer): ?CampaignApplication
    {
        return CampaignApplication::query()
            ->where('campaign_id', $campaign->id)
            ->where('influencer_id', $influencer->id)
            ->first();
    }

    public function create(array $data): CampaignApplication
    {
        return CampaignApplication::create($data);
    }

    public function paginateForInfluencer(User $influencer, int $page, int $size): LengthAwarePaginator
    {
        return CampaignApplication::query()
            ->with(['campaign.product'])
            ->where('influencer_id', $influencer->id)
            ->latest()
            ->paginate($size, ['*'], 'page', $page);
    }

    public function paginateForCampaign(Campaign $campaign, int $page, int $size): LengthAwarePaginator
    {
        return CampaignApplication::query()
            ->with(['influencer'])
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->paginate($size, ['*'], 'page', $page);
    }
}