<?php

declare(strict_types=1);

namespace App\Application\UseCases\CampaignApplications;

use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListInfluencerApplicationsUseCase
{
    public function __construct(
        private readonly CampaignApplicationRepositoryInterface $applications
    ) {}

    public function execute(User $influencer, int $page, int $size): LengthAwarePaginator
    {
        return $this->applications->paginateForInfluencer($influencer, $page, $size);
    }
}