<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Application\Dtos\CampaignApplication\ApplyToCampaignDTO;
use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Domain\Contracts\NotificationServiceInterface;
use App\Enums\ApplicationStatus;
use App\Enums\CampaignStatus;
use App\Enums\NotificationType;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplyToCampaignUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly CampaignApplicationRepositoryInterface $applications,
        private readonly NotificationServiceInterface $notifications,
    ) {}

    public function execute(User $influencer, string $campaignId, ApplyToCampaignDTO $dto): CampaignApplication
    {
        $campaign = $this->campaigns->findById($campaignId);
        if (! $campaign) {
            throw new NotFoundHttpException('Campaign not found.');
        }

        if ($campaign->status !== CampaignStatus::Published) {
            throw new ConflictHttpException('Campaign is not accepting applications.');
        }

        $existing = $this->applications->findByCampaignAndInfluencer($campaign, $influencer);
        if ($existing) {
            throw new ConflictHttpException('You already applied to this campaign.');
        }

        $application = $this->applications->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
            'message' => $dto->message,
            'status' => ApplicationStatus::Pending->value,
        ]);

        try {
            $this->notifications->notify(
                userId: (string) $campaign->brand_id,
                type: NotificationType::CampaignApplied->value,
                title: 'New application received',
                body: 'An influencer applied to your campaign: ' . ($campaign->title ?? 'campaign'),
                actorId: (string) $influencer->id,
                entityType: 'campaign_application',
                entityId: (string) $application->id,
                data: [
                    'campaign_id' => (string) $campaign->id,
                    'campaign_title' => (string) ($campaign->title ?? ''),
                    'application_id' => (string) $application->id,
                    'influencer_id' => (string) $influencer->id,
                ],
            );
        } catch (\Throwable $e) {
            Log::warning('Notification failed on apply', [
                'campaign_id' => (string) $campaign->id,
                'application_id' => (string) $application->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $application->fresh(['campaign.product', 'influencer']);
    }
}