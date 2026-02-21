<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignTiers;

use App\Application\Dtos\CampaignTier\CreateCampaignTierDTO;
use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateCampaignTierUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly CampaignPayoutTierRepositoryInterface $tiers
    ) {}

    public function execute(User $brand, string $campaignId, CreateCampaignTierDTO $dto)
    {
        $campaign = $this->campaigns->findById($campaignId);
        if (! $campaign) {
            throw new NotFoundHttpException('Campaign not found.');
        }

        if ($campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        return $this->tiers->create([
            'campaign_id' => $campaign->id,
            'metric' => $dto->metric,
            'from_value' => $dto->fromValue,
            'to_value' => $dto->toValue,
            'payout_amount' => $dto->payoutAmount,
            'currency' => $dto->currency ?? 'MAD',
        ]);
    }
}