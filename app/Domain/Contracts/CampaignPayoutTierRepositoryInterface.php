<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\CampaignPayoutTier;
use Illuminate\Support\Collection;

interface CampaignPayoutTierRepositoryInterface
{
    public function listForCampaign(string $campaignId): Collection;

    public function findById(string $id): ?CampaignPayoutTier;

    public function create(array $data): CampaignPayoutTier;

    public function update(CampaignPayoutTier $tier, array $data): void;

    public function delete(CampaignPayoutTier $tier): void;
}