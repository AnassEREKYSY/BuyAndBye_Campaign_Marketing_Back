<?php

declare(strict_types=1);

namespace App\Application\UseCases\Collaborations;

use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListMyCollaborationsUseCase
{
    public function __construct(
        private readonly CollaborationRepositoryInterface $collaborations
    ) {}

    public function execute(User $user, int $page, int $size): LengthAwarePaginator
    {
        if ($user->role->isBrand()) {
            return $this->collaborations->paginateForBrand($user, $page, $size);
        }

        return $this->collaborations->paginateForInfluencer($user, $page, $size);
    }
}