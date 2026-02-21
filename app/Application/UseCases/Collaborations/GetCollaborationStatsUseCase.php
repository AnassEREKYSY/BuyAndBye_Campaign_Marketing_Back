<?php

declare(strict_types=1);

namespace App\Application\UseCases\Collaborations;

use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCollaborationStatsUseCase
{
    public function __construct(
        private readonly CollaborationRepositoryInterface $collaborations,
        private readonly ClickEventRepositoryInterface $clicks
    ) {}

    public function execute(User $user, string $collaborationId): array
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

        return [
            'collaboration_id' => $collab->id,
            'campaign_id' => $collab->campaign_id,
            'influencer_id' => $collab->influencer_id,
            'tracking_code' => $collab->trackingLink?->code,
            'clicks' => $trackingId ? $this->clicks->countByTrackingLinkId($trackingId) : 0,
        ];
    }
}