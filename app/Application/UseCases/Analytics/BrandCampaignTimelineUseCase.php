<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrandCampaignTimelineUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly ClickEventRepositoryInterface $clicks
    ) {}

    public function execute(User $brand, string $campaignId, string $from, string $to, string $group): array
    {
        $campaign = $this->campaigns->findById($campaignId);

        if (! $campaign) {
            throw new NotFoundHttpException('Campaign not found.');
        }

        if ($campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        return $this->clicks->timelineByCampaign($campaign->id, $from, $to, $group);
    }
}