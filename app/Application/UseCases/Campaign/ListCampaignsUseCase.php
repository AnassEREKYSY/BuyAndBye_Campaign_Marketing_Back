<?php

declare(strict_types=1);

namespace App\Application\UseCases\Campaign;

use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCampaignsUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns
    ) {}

    public function execute(User $user, int $page, int $size, ?string $status = null, string $scope = 'mine'): LengthAwarePaginator
    {
        if ($user->role->isBrand()) {
            if ($scope === 'all') {
                return $this->campaigns->paginate($page, $size, $status, null);
            }

            return $this->campaigns->paginateForBrand($user, $page, $size, $status);
        }

        return $this->campaigns->paginate($page, $size, $status, null);
    }
}