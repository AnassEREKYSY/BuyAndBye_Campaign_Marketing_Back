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
        $format = $this->dateFormat($group);

        return ClickEvent::query()
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as d, COUNT(*) as clicks_total, COUNT(DISTINCT unique_key) as clicks_unique")
            ->where('campaign_id', $campaignId)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$format}')"))
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => ['date' => $r->d, 'clicks_total' => (int) $r->clicks_total, 'clicks_unique' => (int) $r->clicks_unique])
            ->all();
    }

    public function timelineByTrackingLink(string $trackingLinkId, string $from, string $to, string $group): array
    {
        $format = $this->dateFormat($group);

        return ClickEvent::query()
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as d, COUNT(*) as clicks_total, COUNT(DISTINCT unique_key) as clicks_unique")
            ->where('tracking_link_id', $trackingLinkId)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$format}')"))
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => ['date' => $r->d, 'clicks_total' => (int) $r->clicks_total, 'clicks_unique' => (int) $r->clicks_unique])
            ->all();
    }

    private function dateFormat(string $group): string
    {
        return match ($group) {
            'day' => '%Y-%m-%d',
            default => '%Y-%m-%d',
        };
    }
}