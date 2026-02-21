<?php

declare(strict_types=1);

namespace App\Application\UseCases\Payouts;

use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\CollaborationPayoutRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CloseCollaborationPayoutUseCase
{
    public function __construct(
        private readonly CollaborationRepositoryInterface $collaborations,
        private readonly ClickEventRepositoryInterface $clicks,
        private readonly CampaignPayoutTierRepositoryInterface $tiers,
        private readonly CollaborationPayoutRepositoryInterface $payouts
    ) {}

    public function execute(User $brand, string $collaborationId, string $start, string $end)
    {
        $collab = $this->collaborations->findById($collaborationId);
        if (! $collab) {
            throw new NotFoundHttpException('Collaboration not found.');
        }

        if ($collab->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        if ($this->payouts->existsForPeriod($collab->id, $start, $end)) {
            throw new ConflictHttpException('Payout already closed for this period.');
        }

        $trackingId = $collab->trackingLink?->id;
        $total = 0;
        $unique = 0;

        if ($trackingId) {
            $timeline = $this->clicks->timelineByTrackingLink($trackingId, $start, $end, 'day');
            $total = array_sum(array_map(fn ($r) => (int) $r['clicks_total'], $timeline));
            $unique = array_sum(array_map(fn ($r) => (int) $r['clicks_unique'], $timeline));
        }

        $tiers = $this->tiers->listForCampaign($collab->campaign_id);
        $matched = $this->matchTier($tiers, $unique);

        $amount = $matched ? (float) $matched['payout_amount'] : 0.0;
        $currency = $matched ? $matched['currency'] : 'MAD';

        return $this->payouts->create([
            'collaboration_id' => $collab->id,
            'period_start' => $start,
            'period_end' => $end,
            'clicks_total' => $total,
            'clicks_unique' => $unique,
            'tier_id' => $matched ? $matched['id'] : null,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);
    }

    private function matchTier($tiers, int $value): ?array
    {
        $matched = null;

        foreach ($tiers as $tier) {
            $from = (int) $tier->from_value;
            $to = $tier->to_value !== null ? (int) $tier->to_value : null;

            if ($value >= $from && ($to === null || $value <= $to)) {
                $matched = [
                    'id' => $tier->id,
                    'payout_amount' => (float) $tier->payout_amount,
                    'currency' => $tier->currency,
                ];
            }
        }

        return $matched;
    }
}