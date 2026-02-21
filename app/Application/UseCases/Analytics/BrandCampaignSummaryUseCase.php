<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrandCampaignSummaryUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly CollaborationRepositoryInterface $collaborations,
        private readonly ClickEventRepositoryInterface $clicks,
        private readonly CampaignPayoutTierRepositoryInterface $tiers
    ) {}

    public function execute(User $brand, string $campaignId): array
    {
        $campaign = $this->campaigns->findById($campaignId);

        if (! $campaign) {
            throw new NotFoundHttpException('Campaign not found.');
        }

        if ($campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $collabs = $this->collaborations->listForCampaign($campaign->id);

        $trackingIds = [];
        foreach ($collabs as $c) {
            if ($c->trackingLink?->id) {
                $trackingIds[] = $c->trackingLink->id;
            }
        }

        $clicksByTracking = $this->clicks->groupCountsByTrackingLinkIds($trackingIds);
        $totalClicks = $this->clicks->countByCampaignId($campaign->id);

        $tiers = $this->tiers->listForCampaign($campaign->id);

        $items = [];
        foreach ($collabs as $c) {
            $trackingId = $c->trackingLink?->id;
            $count = $trackingId ? (int) ($clicksByTracking[$trackingId] ?? 0) : 0;

            $matchedTier = $this->matchTier($tiers, $count);

            $items[] = [
                'collaboration_id' => $c->id,
                'influencer' => $c->influencer ? [
                    'id' => $c->influencer->id,
                    'display_name' => $c->influencer->display_name,
                    'photo_url' => $c->influencer->photo_url,
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
                'payout_amount' => $matchedTier ? (float) $matchedTier['payout_amount'] : 0.0,
                'currency' => $matchedTier ? $matchedTier['currency'] : 'MAD',
            ];
        }

        usort($items, fn ($a, $b) => ($b['clicks'] <=> $a['clicks']));

        return [
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'status' => $campaign->status->value,
                'product_id' => $campaign->product_id,
            ],
            'totals' => [
                'clicks' => $totalClicks,
                'collaborations' => count($items),
            ],
            'tiers' => $tiers->map(fn ($t) => [
                'id' => $t->id,
                'metric' => $t->metric,
                'from_value' => (int) $t->from_value,
                'to_value' => $t->to_value !== null ? (int) $t->to_value : null,
                'payout_amount' => (float) $t->payout_amount,
                'currency' => $t->currency,
            ])->values()->all(),
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