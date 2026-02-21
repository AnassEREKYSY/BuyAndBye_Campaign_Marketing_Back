<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignTiers;

use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use Illuminate\Support\Collection;

class ListCampaignTiersUseCase
{
    public function __construct(
        private readonly CampaignPayoutTierRepositoryInterface $tiers
    ) {}

    public function execute(string $campaignId): Collection
    {
        return $this->tiers->listForCampaign($campaignId);
    }
}