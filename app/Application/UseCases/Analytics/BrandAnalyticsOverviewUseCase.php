<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Application\UseCases\Analytics\Concerns\BuildsClickAnalytics;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Cross-campaign analytics for a brand: totals, daily clicks, campaign and creator rankings,
 * traffic sources, devices and payouts.
 */
class BrandAnalyticsOverviewUseCase
{
    use BuildsClickAnalytics;

    public function execute(User $brand, int $days): array
    {
        $days = $this->normalizeDays($days);
        [$from, $to, $previousFrom] = $this->window($days);
        $brandId = $brand->id;

        $scoped = fn () => DB::table('click_events as ce')
            ->join('campaigns as c', 'c.id', '=', 'ce.campaign_id')
            ->where('c.brand_id', $brandId)
            ->whereNull('c.deleted_at');

        $campaignCounts = DB::table('campaigns')
            ->where('brand_id', $brandId)
            ->whereNull('deleted_at')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $activeCollaborations = (int) DB::table('collaborations')
            ->where('brand_id', $brandId)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->count();

        $pendingApplications = (int) DB::table('campaign_applications as a')
            ->join('campaigns as c', 'c.id', '=', 'a.campaign_id')
            ->where('c.brand_id', $brandId)
            ->whereIn('a.status', ['pending', 'shortlisted'])
            ->whereNull('a.deleted_at')
            ->count();

        $campaigns = DB::table('campaigns as c')
            ->where('c.brand_id', $brandId)
            ->whereNull('c.deleted_at')
            ->leftJoin('click_events as ce', function ($join) use ($from, $to) {
                $join->on('ce.campaign_id', '=', 'c.id')->whereBetween('ce.created_at', [$from, $to]);
            })
            ->selectRaw('c.id, c.title, c.status, c.budget, count(ce.id) as clicks, count(distinct ce.unique_key) as unique_clicks')
            ->groupBy('c.id', 'c.title', 'c.status', 'c.budget')
            ->orderByDesc('clicks')
            ->get();

        $collabCounts = DB::table('collaborations')
            ->where('brand_id', $brandId)
            ->whereNull('deleted_at')
            ->selectRaw('campaign_id, count(*) as total')
            ->groupBy('campaign_id')
            ->pluck('total', 'campaign_id');

        $topCreators = $scoped()
            ->whereBetween('ce.created_at', [$from, $to])
            ->join('users as u', 'u.id', '=', 'ce.influencer_id')
            ->selectRaw('u.id, u.display_name, u.photo_url, count(*) as clicks, count(distinct ce.campaign_id) as campaigns')
            ->groupBy('u.id', 'u.display_name', 'u.photo_url')
            ->orderByDesc('clicks')
            ->limit(6)
            ->get();

        $payouts = DB::table('collaboration_payouts as p')
            ->join('collaborations as co', 'co.id', '=', 'p.collaboration_id')
            ->where('co.brand_id', $brandId)
            ->whereNull('p.deleted_at')
            ->selectRaw('p.status, sum(p.amount) as amount, max(p.currency) as currency')
            ->groupBy('p.status')
            ->get()
            ->keyBy('status');

        return [
            'range' => ['days' => $days, 'from' => $from->toDateString(), 'to' => $to->toDateString()],
            'totals' => $this->totals($scoped, $from, $to, $previousFrom) + [
                'campaigns_published' => (int) ($campaignCounts['published'] ?? 0),
                'campaigns_draft' => (int) ($campaignCounts['draft'] ?? 0),
                'campaigns_closed' => (int) ($campaignCounts['closed'] ?? 0),
                'active_collaborations' => $activeCollaborations,
                'pending_applications' => $pendingApplications,
            ],
            'series' => $this->dailySeries($scoped, $from, $to),
            'campaigns' => $campaigns->map(fn ($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'status' => $c->status,
                'budget' => $c->budget !== null ? (float) $c->budget : null,
                'clicks' => (int) $c->clicks,
                'unique_clicks' => (int) $c->unique_clicks,
                'collaborators' => (int) ($collabCounts[$c->id] ?? 0),
            ])->values(),
            'top_creators' => $topCreators->map(fn ($u) => [
                'id' => $u->id,
                'display_name' => $u->display_name,
                'photo_url' => $u->photo_url,
                'clicks' => (int) $u->clicks,
                'campaigns' => (int) $u->campaigns,
            ])->values(),
            'sources' => $this->sources($scoped, $from, $to),
            'devices' => $this->devices($scoped, $from, $to),
            'payouts' => [
                'currency' => $payouts->first()->currency ?? 'MAD',
                'pending' => (float) ($payouts['pending']->amount ?? 0),
                'approved' => (float) ($payouts['approved']->amount ?? 0),
                'paid' => (float) ($payouts['paid']->amount ?? 0),
            ],
        ];
    }
}
