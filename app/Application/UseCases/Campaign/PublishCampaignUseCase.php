<?php

declare(strict_types=1);

namespace App\Application\UseCases\Campaign;

use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Enums\CampaignStatus;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublishCampaignUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns
    ) {}

    public function execute(User $brand, string $campaignId)
    {
        $campaign = $this->campaigns->findById($campaignId);
        if (! $campaign) {
            throw new NotFoundHttpException('Campaign not found.');
        }

        if ($campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $this->campaigns->update($campaign, [
            'status' => CampaignStatus::Published->value,
        ]);

        return $campaign->fresh(['product', 'brand']);
    }
}