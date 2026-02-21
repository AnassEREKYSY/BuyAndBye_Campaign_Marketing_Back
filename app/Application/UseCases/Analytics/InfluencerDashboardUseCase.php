<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;

class InfluencerDashboardUseCase
{
    public function __construct(
        private readonly CollaborationRepositoryInterface $collaborations,
        private readonly ClickEventRepositoryInterface $clicks,
        private readonly CampaignPayoutTierRepositoryInterface $tiers
    ) {}

    public function execute(User $influencer): array
    {
        $collabs = $this->collaborations->listForInfluencer($influencer->id);

        $trackingIds = [];
        foreach ($collabs as $c) {
            if ($c->trackingLink?->id) {
                $trackingIds[] = $c->trackingLink->id;
            }
        }

        $clicksByTracking = $this->clicks->groupCountsByTrackingLinkIds($trackingIds);

        $items = [];
        $totalClicks = 0;
        $estimatedTotalPayout = 0.0;

        foreach ($collabs as $c) {
            $trackingId = $c->trackingLink?->id;
            $count = $trackingId ? (int) ($clicksByTracking[$trackingId] ?? 0) : 0;
            $totalClicks += $count;

            $tiers = $this->tiers->listForCampaign($c->campaign_id);
            $matchedTier = $this->matchTier($tiers, $count);

            $payout = $matchedTier ? (float) $matchedTier['payout_amount'] : 0.0;
            $estimatedTotalPayout += $payout;

            $items[] = [
                'collaboration_id' => $c->id,
                'campaign' => $c->campaign ? [
                    'id' => $c->campaign->id,
                    'title' => $c->campaign->title,
                    'status' => $c->campaign->status->value,
                ] : null,
                'brand' => $c->brand ? [
                    'id' => $c->brand->id,
                    'display_name' => $c->brand->display_name,
                    'photo_url' => $c->brand->photo_url,
                ] : null,
                'tracking' => [
                    'code' => $c->trackingLink?->code,
                    'url' => $c->trackingLink?->code ? url("/api/v1/t/{$c->trackingLink->code}") : null,
                ],
                'promo' => [
                    'code' => $c->promoCode?->code,
                ],
                'clicks' => $count,
                'tier' => $matchedTier,
                'payout_amount' => $payout,
                'currency' => $matchedTier ? $matchedTier['currency'] : 'MAD',
            ];
        }

        usort($items, fn ($a, $b) => ($b['clicks'] <=> $a['clicks']));

        return [
            'influencer' => [
                'id' => $influencer->id,
                'display_name' => $influencer->display_name,
                'photo_url' => $influencer->photo_url,
            ],
            'totals' => [
                'clicks' => $totalClicks,
                'estimated_payout' => $estimatedTotalPayout,
                'currency' => 'MAD',
                'collaborations' => count($items),
            ],
            'collaborations' => $items,
        ];
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
                    'from_value' => $from,
                    'to_value' => $to,
                    'payout_amount' => (float) $tier->payout_amount,
                    'currency' => $tier->currency,
                ];
            }
        }

        return $matched;
    }
}