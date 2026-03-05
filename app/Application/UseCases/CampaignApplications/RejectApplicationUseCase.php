<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\ConversationRepositoryInterface;
use App\Domain\Contracts\NotificationServiceInterface;
use App\Enums\ApplicationStatus;
use App\Enums\NotificationType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RejectApplicationUseCase
{
    public function __construct(
        private readonly CampaignApplicationRepositoryInterface $applications,
        private readonly NotificationServiceInterface $notifications,
        private readonly ConversationRepositoryInterface $conversations,
    ) {}

    public function execute(User $brand, string $applicationId)
    {
        return DB::transaction(function () use ($brand, $applicationId) {
            $application = $this->applications->findById($applicationId);
            if (! $application) {
                throw new NotFoundHttpException('Application not found.');
            }

            $campaign = $application->campaign;

            if (! $campaign || $campaign->brand_id !== $brand->id) {
                throw new ForbiddenHttpException('Not allowed.');
            }

            if (! in_array($application->status, [ApplicationStatus::Pending, ApplicationStatus::Shortlisted], true)) {
                throw new ConflictHttpException('Only pending or shortlisted applications can be rejected.');
            }

            if ($this->applications->findAcceptedForCampaign($campaign->id)) {
                throw new ConflictHttpException('Campaign is already finalized.');
            }

            $this->applications->updateStatus($application, ApplicationStatus::Rejected);

            $conversation = $this->conversations->findByApplicationId((string) $application->id);
            if ($conversation) {
                $this->conversations->closeConversation((string) $conversation->id, 'application_rejected');
            }

            $this->notifications->notify(
                userId: (string) $application->influencer_id,
                type: NotificationType::ApplicationRejected->value,
                title: 'Application rejected',
                body: 'Your application was rejected for: ' . ($campaign->title ?? 'campaign'),
                data: [
                    'campaign_id' => (string) $campaign->id,
                    'application_id' => (string) $application->id,
                ],
                actorId: (string) $brand->id,
                entityType: 'CampaignApplication',
                entityId: (string) $application->id
            );

            return $application->fresh(['campaign.product', 'influencer']);
        });
    }
}