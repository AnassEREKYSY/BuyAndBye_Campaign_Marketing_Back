<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Enums\ApplicationStatus;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RejectApplicationUseCase
{
    public function __construct(
        private readonly CampaignApplicationRepositoryInterface $applications
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

        if (! in_array($application->status, [ApplicationStatus::Pending, ApplicationStatus::Shortlisted], true)) {
            throw new ConflictHttpException('Only pending or shortlisted applications can be rejected.');
        }

        if ($this->applications->findAcceptedForCampaign($campaign->id)) {
            throw new ConflictHttpException('Campaign is already finalized.');
        }

        $this->applications->updateStatus($application, ApplicationStatus::Rejected);

        return $application->fresh(['campaign.product', 'influencer']);
    }
}