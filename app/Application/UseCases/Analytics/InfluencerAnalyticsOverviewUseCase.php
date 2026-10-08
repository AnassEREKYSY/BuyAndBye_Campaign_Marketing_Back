<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Application\UseCases\Analytics\Concerns\BuildsClickAnalytics;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creator-side analytics: clicks over time, every tracked link / promo code with its numbers,
 * and earnings grouped by payout status.
 */
class InfluencerAnalyticsOverviewUseCase
{
    use BuildsClickAnalytics;

    public function execute(User $influencer, int $days): array
    {
        $days = $this->normalizeDays($days);
        [$from, $to, $previousFrom] = $this->window($days);
        $userId = $influencer->id;

        $scoped = fn () => DB::table('click_events as ce')->where('ce.influencer_id', $userId);

        $links = DB::table('collaborations as co')
            ->join('campaigns as c', 'c.id', '=', 'co.campaign_id')
            ->leftJoin('brand_profiles as bp', 'bp.user_id', '=', 'co.brand_id')
            ->leftJoin('users as b', 'b.id', '=', 'co.brand_id')
            ->leftJoin('tracking_links as tl', function ($j) {
                $j->on('tl.collaboration_id', '=', 'co.id')->whereNull('tl.deleted_at');
            })
            ->leftJoin('promo_codes as pc', function ($j) {
                $j->on('pc.collaboration_id', '=', 'co.id')->whereNull('pc.deleted_at');
            })
            ->where('co.influencer_id', $userId)
            ->whereNull('co.deleted_at')
            ->select([
                'co.id as collaboration_id', 'co.status', 'co.accepted_at',
                'c.id as campaign_id', 'c.title as campaign_title', 'c.commission_type', 'c.commission_value',
                DB::raw('coalesce(bp.brand_name, b.display_name) as brand_name'),
                'tl.id as tracking_link_id', 'tl.code as tracking_code', 'tl.destination_url',
                'pc.code as promo_code',
            ])
            ->orderByDesc('co.accepted_at')
            ->get();

        $linkIds = $links->pluck('tracking_link_id')->filter()->values()->all();

        $clicksAll = $linkIds ? DB::table('click_events')->whereIn('tracking_link_id', $linkIds)
            ->selectRaw('tracking_link_id, count(*) as total')->groupBy('tracking_link_id')->pluck('total', 'tracking_link_id') : collect();

        $clicksPeriod = $linkIds ? DB::table('click_events')->whereIn('tracking_link_id', $linkIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('tracking_link_id, count(*) as total')->groupBy('tracking_link_id')->pluck('total', 'tracking_link_id') : collect();

        $payoutRows = DB::table('collaboration_payouts as p')
            ->join('collaborations as co', 'co.id', '=', 'p.collaboration_id')
            ->join('campaigns as c', 'c.id', '=', 'co.campaign_id')
            ->where('co.influencer_id', $userId)
            ->whereNull('p.deleted_at')
            ->select(['p.id', 'p.period_start', 'p.period_end', 'p.clicks_total', 'p.clicks_unique', 'p.amount', 'p.currency', 'p.status', 'p.created_at', 'c.title as campaign_title'])
            ->orderByDesc('p.period_end')
            ->get();

        $byStatus = ['pending' => 0.0, 'approved' => 0.0, 'paid' => 0.0];
        foreach ($payoutRows as $p) {
            $byStatus[$p->status] = ($byStatus[$p->status] ?? 0) + (float) $p->amount;
        }

        $monthly = [];
        foreach ($payoutRows as $p) {
            $key = substr((string) $p->period_end, 0, 7);
            $monthly[$key] = ($monthly[$key] ?? 0) + (float) $p->amount;
        }
        ksort($monthly);

        return [
            'range' => ['days' => $days, 'from' => $from->toDateString(), 'to' => $to->toDateString()],
            'totals' => $this->totals($scoped, $from, $to, $previousFrom) + [
                'active_collaborations' => $links->where('status', 'active')->count(),
            ],
            'series' => $this->dailySeries($scoped, $from, $to),
            'sources' => $this->sources($scoped, $from, $to),
            'devices' => $this->devices($scoped, $from, $to),
            'links' => $links->map(fn ($l) => [
                'collaboration_id' => $l->collaboration_id,
                'status' => $l->status,
                'accepted_at' => $l->accepted_at,
                'campaign' => ['id' => $l->campaign_id, 'title' => $l->campaign_title],
                'brand_name' => $l->brand_name,
                'commission' => ['type' => $l->commission_type, 'value' => (float) $l->commission_value],
                'tracking' => [
                    'code' => $l->tracking_code,
                    'url' => $this->trackingUrl($l->tracking_code),
                    'destination_url' => $l->destination_url,
                ],
                'promo_code' => $l->promo_code,
                'clicks' => (int) ($clicksAll[$l->tracking_link_id] ?? 0),
                'clicks_in_range' => (int) ($clicksPeriod[$l->tracking_link_id] ?? 0),
            ])->values(),
            'earnings' => [
                'currency' => $payoutRows->first()->currency ?? 'MAD',
                'pending' => $byStatus['pending'],
                'approved' => $byStatus['approved'],
                'paid' => $byStatus['paid'],
                'total' => array_sum($byStatus),
                'monthly' => collect($monthly)->map(fn ($amount, $month) => ['month' => $month, 'amount' => round($amount, 2)])->values(),
                'payouts' => $payoutRows->take(20)->map(fn ($p) => [
                    'id' => $p->id,
                    'campaign_title' => $p->campaign_title,
                    'period_start' => $p->period_start,
                    'period_end' => $p->period_end,
                    'clicks_total' => (int) $p->clicks_total,
                    'clicks_unique' => (int) $p->clicks_unique,
                    'amount' => (float) $p->amount,
                    'currency' => $p->currency,
                    'status' => $p->status,
                ])->values(),
            ],
        ];
    }
}
