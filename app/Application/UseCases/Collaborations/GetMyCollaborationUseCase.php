<?php

declare(strict_types=1);

namespace App\Application\UseCases\Collaborations;

use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetMyCollaborationUseCase
{
    public function __construct(
        private readonly CollaborationRepositoryInterface $collaborations
    ) {}

    public function execute(User $user, string $id)
    {
        $collab = $this->collaborations->findById($id);

        if (! $collab) {
            throw new NotFoundHttpException('Collaboration not found.');
        }

        $allowed =
            ($user->role->isBrand() && $collab->brand_id === $user->id) ||
            ($user->role->isInfluencer() && $collab->influencer_id === $user->id) ||
            $user->role->isAdmin();

        if (! $allowed) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        return $collab;
    }
}