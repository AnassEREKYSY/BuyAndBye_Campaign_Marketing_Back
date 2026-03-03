<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\NotificationServiceInterface;
use App\Enums\ApplicationStatus;
use App\Enums\NotificationType;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortlistApplicationUseCase
{
    public function __construct(
        private readonly CampaignApplicationRepositoryInterface $applications,
        private readonly NotificationServiceInterface $notifications,
    ) {}

    public function execute(User $brand, string $applicationId)
    {
        $application = $this->applications->findById($applicationId);
        if (! $application) {
            throw new NotFoundHttpException('Application not found.');
        }

        $campaign = $application->campaign;

        if (! $campaign || $campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        if ($application->status !== ApplicationStatus::Pending) {
            throw new ConflictHttpException('Only pending applications can be shortlisted.');
        }

        if ($this->applications->findAcceptedForCampaign($campaign->id)) {
            throw new ConflictHttpException('Campaign is already finalized.');
        }

        $this->applications->updateStatus($application, ApplicationStatus::Shortlisted);

        $this->notifications->notify(
            userId: (string) $application->influencer_id,
            type: NotificationType::ApplicationShortlisted->value,
            title: 'You were shortlisted',
            body: 'Your application was shortlisted for: ' . ($campaign->title ?? 'campaign'),
            data: [
                'campaign_id' => (string) $campaign->id,
                'application_id' => (string) $application->id,
            ],
            actorId: (string) $brand->id,
            entityType: 'CampaignApplication',
            entityId: (string) $application->id
        );

        return $application->fresh(['campaign.product', 'influencer']);
    }
}