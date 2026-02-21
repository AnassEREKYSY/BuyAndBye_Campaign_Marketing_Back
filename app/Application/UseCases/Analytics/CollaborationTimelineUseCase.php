<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CollaborationTimelineUseCase
{
    public function __construct(
        private readonly CollaborationRepositoryInterface $collaborations,
        private readonly ClickEventRepositoryInterface $clicks
    ) {}

    public function execute(User $user, string $collaborationId, string $from, string $to, string $group): array
    {
        $collab = $this->collaborations->findById($collaborationId);

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

        $trackingId = $collab->trackingLink?->id;
        if (! $trackingId) {
            return [];
        }

        return $this->clicks->timelineByTrackingLink($trackingId, $from, $to, $group);
    }
}