<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Enums\ApplicationStatus;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortlistApplicationUseCase
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

        if ($application->status !== ApplicationStatus::Pending) {
            throw new ConflictHttpException('Only pending applications can be shortlisted.');
        }

        if ($this->applications->findAcceptedForCampaign($campaign->id)) {
            throw new ConflictHttpException('Campaign is already finalized.');
        }

        $this->applications->updateStatus($application, ApplicationStatus::Shortlisted);

        return $application->fresh(['campaign.product', 'influencer']);
    }
}