<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CampaignRepositoryInterface
{
    public function paginate(int $page, int $size, ?string $status = null, ?string $brandId = null): LengthAwarePaginator;

    public function paginateForBrand(User $brand, int $page, int $size, ?string $status = null): LengthAwarePaginator;

    public function findById(string $id): ?Campaign;

    public function create(array $data): Campaign;

    public function update(Campaign $campaign, array $data): void;

    public function delete(Campaign $campaign): void;
}