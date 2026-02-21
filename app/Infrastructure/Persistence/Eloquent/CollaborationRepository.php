<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\Collaboration;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    public function findById(string $id): ?Collaboration
    {
        return Collaboration::query()
            ->with(['trackingLink', 'promoCode', 'campaign.product', 'brand', 'influencer'])
            ->where('id', $id)
            ->first();
    }

    public function paginateForBrand(User $brand, int $page, int $size): LengthAwarePaginator
    {
        return Collaboration::query()
            ->with(['trackingLink', 'promoCode', 'campaign.product', 'influencer'])
            ->where('brand_id', $brand->id)
            ->latest()
            ->paginate($size, ['*'], 'page', $page);
    }

    public function paginateForInfluencer(User $influencer, int $page, int $size): LengthAwarePaginator
    {
        return Collaboration::query()
            ->with(['trackingLink', 'promoCode', 'campaign.product', 'brand'])
            ->where('influencer_id', $influencer->id)
            ->latest()
            ->paginate($size, ['*'], 'page', $page);
    }

    public function listForCampaign(string $campaignId): Collection
    {
        return Collaboration::query()
            ->with(['trackingLink', 'promoCode', 'campaign.product', 'influencer'])
            ->where('campaign_id', $campaignId)
            ->latest()
            ->get();
    }

    public function listForInfluencer(string $influencerId): Collection
    {
        return Collaboration::query()
            ->with(['trackingLink', 'promoCode', 'campaign.product', 'brand'])
            ->where('influencer_id', $influencerId)
            ->latest()
            ->get();
    }
}