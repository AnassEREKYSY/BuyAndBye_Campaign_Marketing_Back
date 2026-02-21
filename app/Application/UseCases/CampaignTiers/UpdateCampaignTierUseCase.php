<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignTiers;

use App\Application\Dtos\CampaignTier\UpdateCampaignTierDTO;
use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateCampaignTierUseCase
{
    public function __construct(
        private readonly CampaignPayoutTierRepositoryInterface $tiers
    ) {}

    public function execute(User $brand, string $tierId, UpdateCampaignTierDTO $dto)
    {
        $tier = $this->tiers->findById($tierId);
        if (! $tier) {
            throw new NotFoundHttpException('Tier not found.');
        }

        if ($tier->campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $data = array_filter([
            'metric' => $dto->metric,
            'from_value' => $dto->fromValue,
            'to_value' => $dto->toValue,
            'payout_amount' => $dto->payoutAmount,
            'currency' => $dto->currency,
        ], fn ($v) => $v !== null);

        if (! empty($data)) {
            $this->tiers->update($tier, $data);
        }

        return $tier->fresh();
    }
}