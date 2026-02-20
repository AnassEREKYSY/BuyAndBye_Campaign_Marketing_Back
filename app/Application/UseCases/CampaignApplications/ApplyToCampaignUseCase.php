<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Application\Dtos\CampaignApplication\ApplyToCampaignDTO;
use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Enums\ApplicationStatus;
use App\Enums\CampaignStatus;
use App\Models\CampaignApplication;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplyToCampaignUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly CampaignApplicationRepositoryInterface $applications,
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

        return $application->fresh(['campaign.product', 'influencer']);
    }
}