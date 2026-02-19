<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CampaignRepository implements CampaignRepositoryInterface
{
    public function paginate(int $page, int $size, ?string $status = null, ?string $brandId = null): LengthAwarePaginator
    {
        $q = Campaign::query()->with(['product', 'brand']);

        if ($status) {
            $q->where('status', $status);
        }

        if ($brandId) {
            $q->where('brand_id', $brandId);
        }

        return $q->latest()->paginate($size, ['*'], 'page', $page);
    }

    public function paginateForBrand(User $brand, int $page, int $size, ?string $status = null): LengthAwarePaginator
    {
        $q = Campaign::query()
            ->with(['product'])
            ->where('brand_id', $brand->id);

        if ($status) {
            $q->where('status', $status);
        }

        return $q->latest()->paginate($size, ['*'], 'page', $page);
    }

    public function findById(string $id): ?Campaign
    {
        return Campaign::query()->with(['product', 'brand'])->where('id', $id)->first();
    }

    public function create(array $data): Campaign
    {
        return Campaign::create($data);
    }

    public function update(Campaign $campaign, array $data): void
    {
        $campaign->update($data);
    }

    public function delete(Campaign $campaign): void
    {
        $campaign->delete();
    }
}