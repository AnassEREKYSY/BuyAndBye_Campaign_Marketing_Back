<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ListBrandCampaignApplicationsUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly CampaignApplicationRepositoryInterface $applications,
    ) {}

    public function execute(User $brand, string $campaignId, int $page, int $size): LengthAwarePaginator
    {
        $campaign = $this->campaigns->findById($campaignId);
        if (! $campaign) {
            throw new NotFoundHttpException('Campaign not found.');
        }

        if ($campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        return $this->applications->paginateForCampaign($campaign, $page, $size);
    }
}