<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Models\ClickEvent;
use Illuminate\Support\Facades\DB;

class ClickEventRepository implements ClickEventRepositoryInterface
{
    public function create(array $data): ClickEvent
    {
        return ClickEvent::create($data);
    }

    public function countByTrackingLinkId(string $trackingLinkId): int
    {
        return ClickEvent::query()->where('tracking_link_id', $trackingLinkId)->count();
    }

    public function countUniqueByTrackingLinkId(string $trackingLinkId): int
    {
        return (int) ClickEvent::query()
            ->where('tracking_link_id', $trackingLinkId)
            ->whereNotNull('unique_key')
            ->distinct('unique_key')
            ->count('unique_key');
    }

    public function countByCampaignId(string $campaignId): int
    {
        return ClickEvent::query()->where('campaign_id', $campaignId)->count();
    }

    public function countUniqueByCampaignId(string $campaignId): int
    {
        return (int) ClickEvent::query()
            ->where('campaign_id', $campaignId)
            ->whereNotNull('unique_key')
            ->distinct('unique_key')
            ->count('unique_key');
    }

    public function groupCountsByTrackingLinkIds(array $trackingLinkIds): array
    {
        if (empty($trackingLinkIds)) return [];

        return ClickEvent::query()
            ->selectRaw('tracking_link_id, COUNT(*) as cnt')
            ->whereIn('tracking_link_id', $trackingLinkIds)
            ->groupBy('tracking_link_id')
            ->pluck('cnt', 'tracking_link_id')
            ->toArray();
    }

    public function groupUniqueCountsByTrackingLinkIds(array $trackingLinkIds): array
    {
        if (empty($trackingLinkIds)) return [];

        return ClickEvent::query()
            ->selectRaw('tracking_link_id, COUNT(DISTINCT unique_key) as cnt')
            ->whereIn('tracking_link_id', $trackingLinkIds)
            ->whereNotNull('unique_key')
            ->groupBy('tracking_link_id')
            ->pluck('cnt', 'tracking_link_id')
            ->toArray();
    }

    public function timelineByCampaign(string $campaignId, string $from, string $to, string $group): array
    {
        [$selectExpr, $groupExpr] = $this->groupExpr($group);

        return ClickEvent::query()
            ->selectRaw("$selectExpr as d, COUNT(*) as clicks_total, COUNT(DISTINCT unique_key) as clicks_unique")
            ->where('campaign_id', $campaignId)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw($groupExpr))
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => [
                'date' => (string) $r->d,
                'clicks_total' => (int) $r->clicks_total,
                'clicks_unique' => (int) $r->clicks_unique,
            ])
            ->all();
    }

    public function timelineByTrackingLink(string $trackingLinkId, string $from, string $to, string $group): array
    {
        [$selectExpr, $groupExpr] = $this->groupExpr($group);

        return ClickEvent::query()
            ->selectRaw("$selectExpr as d, COUNT(*) as clicks_total, COUNT(DISTINCT unique_key) as clicks_unique")
            ->where('tracking_link_id', $trackingLinkId)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw($groupExpr))
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => [
                'date' => (string) $r->d,
                'clicks_total' => (int) $r->clicks_total,
                'clicks_unique' => (int) $r->clicks_unique,
            ])
            ->all();
    }

    private function groupExpr(string $group): array
    {
        $driver = DB::getDriverName();
        $group = $group ?: 'day';

        if ($driver === 'pgsql') {
            return match ($group) {
                'day' => ["to_char(created_at, 'YYYY-MM-DD')", "to_char(created_at, 'YYYY-MM-DD')"],
                default => ["to_char(created_at, 'YYYY-MM-DD')", "to_char(created_at, 'YYYY-MM-DD')"],
            };
        }

        if ($driver === 'sqlite') {
            return match ($group) {
                'day' => ["strftime('%Y-%m-%d', created_at)", "strftime('%Y-%m-%d', created_at)"],
                default => ["strftime('%Y-%m-%d', created_at)", "strftime('%Y-%m-%d', created_at)"],
            };
        }

        return match ($group) {
            'day' => ["DATE_FORMAT(created_at, '%Y-%m-%d')", "DATE_FORMAT(created_at, '%Y-%m-%d')"],
            default => ["DATE_FORMAT(created_at, '%Y-%m-%d')", "DATE_FORMAT(created_at, '%Y-%m-%d')"],
        };
    }
}