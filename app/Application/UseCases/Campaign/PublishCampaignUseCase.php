<?php

declare(strict_types=1);

namespace App\Application\UseCases\Campaign;

use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Domain\Contracts\NotificationServiceInterface;
use App\Enums\CampaignStatus;
use App\Enums\NotificationType;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublishCampaignUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly NotificationServiceInterface $notifications,
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

        $fresh = $campaign->fresh(['product', 'brand']);

        $this->notifications->notify(
            userId: (string) $brand->id,
            type: NotificationType::CampaignPublished->value,
            title: 'Campaign published',
            body: 'Your campaign is now published: ' . ($fresh->title ?? 'campaign'),
            data: [
                'campaign_id' => (string) $fresh->id,
            ],
            actorId: (string) $brand->id,
            entityType: 'Campaign',
            entityId: (string) $fresh->id
        );

        return $fresh;
    }
}