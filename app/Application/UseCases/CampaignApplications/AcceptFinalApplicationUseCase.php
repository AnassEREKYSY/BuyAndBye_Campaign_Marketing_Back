<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Enums\ApplicationStatus;
use App\Enums\CollaborationStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AcceptFinalApplicationUseCase
{
    public function __construct(
        private readonly CampaignApplicationRepositoryInterface $applications,
        private readonly CollaborationRepositoryInterface $collaborations,
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
                throw new ConflictHttpException('Only pending or shortlisted applications can be accepted.');
            }

            if ($this->applications->findAcceptedForCampaign($campaign->id)) {
                throw new ConflictHttpException('Campaign is already finalized.');
            }

            $this->applications->updateStatus($application, ApplicationStatus::Accepted);

            $existingCollab = $this->collaborations->findByCampaignAndInfluencer($campaign->id, $application->influencer_id);
            if (! $existingCollab) {
                $this->collaborations->create([
                    'campaign_id' => $campaign->id,
                    'brand_id' => $campaign->brand_id,
                    'influencer_id' => $application->influencer_id,
                    'accepted_at' => now(),
                    'status' => CollaborationStatus::Active->value,
                ]);
            }

            $this->applications->rejectOthers($campaign->id, $application->id);

            return $application->fresh(['campaign.product', 'influencer']);
        });
    }
}