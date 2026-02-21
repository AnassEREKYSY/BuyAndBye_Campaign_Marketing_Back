<?php

declare(strict_types=1);

namespace App\Application\UseCases\Collaborations;

use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCollaborationPayoutUseCase
{
    public function __construct(
        private readonly CollaborationRepositoryInterface $collaborations,
        private readonly CampaignPayoutTierRepositoryInterface $tiers,
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
        $clickCount = $trackingId ? $this->clicks->countByTrackingLinkId($trackingId) : 0;

        $tiers = $this->tiers->listForCampaign($collab->campaign_id);

        $matched = null;
        foreach ($tiers as $tier) {
            $from = (int) $tier->from_value;
            $to = $tier->to_value !== null ? (int) $tier->to_value : null;

            if ($clickCount >= $from && ($to === null || $clickCount <= $to)) {
                $matched = $tier;
            }
        }

        return [
            'collaboration_id' => $collab->id,
            'campaign_id' => $collab->campaign_id,
            'influencer_id' => $collab->influencer_id,
            'metric' => 'clicks',
            'value' => $clickCount,
            'tier' => $matched ? [
                'id' => $matched->id,
                'from_value' => (int) $matched->from_value,
                'to_value' => $matched->to_value !== null ? (int) $matched->to_value : null,
                'payout_amount' => (float) $matched->payout_amount,
                'currency' => $matched->currency,
            ] : null,
            'payout_amount' => $matched ? (float) $matched->payout_amount : 0.0,
            'currency' => $matched ? $matched->currency : 'MAD',
        ];
    }
}