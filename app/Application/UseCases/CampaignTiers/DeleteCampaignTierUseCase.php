<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignTiers;

use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteCampaignTierUseCase
{
    public function __construct(
        private readonly CampaignPayoutTierRepositoryInterface $tiers
    ) {}

    public function execute(User $brand, string $tierId): void
    {
        $tier = $this->tiers->findById($tierId);
        if (! $tier) {
            throw new NotFoundHttpException('Tier not found.');
        }

        if ($tier->campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $this->tiers->delete($tier);
    }
}