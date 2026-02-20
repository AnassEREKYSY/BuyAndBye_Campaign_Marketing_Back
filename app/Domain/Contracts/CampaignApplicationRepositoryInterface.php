<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Enums\ApplicationStatus;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CampaignApplicationRepositoryInterface
{
    public function findById(string $id): ?CampaignApplication;

    public function findByCampaignAndInfluencer(Campaign $campaign, User $influencer): ?CampaignApplication;

    public function create(array $data): CampaignApplication;

    public function paginateForInfluencer(User $influencer, int $page, int $size): LengthAwarePaginator;

    public function paginateForCampaign(Campaign $campaign, int $page, int $size): LengthAwarePaginator;

    public function updateStatus(CampaignApplication $application, ApplicationStatus $status): void;

    public function findAcceptedForCampaign(string $campaignId): ?CampaignApplication;

    public function rejectOthers(string $campaignId, string $keepApplicationId): int;
}