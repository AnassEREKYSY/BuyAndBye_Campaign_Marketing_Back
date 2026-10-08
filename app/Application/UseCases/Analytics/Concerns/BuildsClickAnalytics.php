<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics\Concerns;

use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * Shared helpers for the analytics overview endpoints.
 * Every method receives a fresh, already-scoped click_events query (aliased "ce").
 */
trait BuildsClickAnalytics
{
    protected function normalizeDays(int $days): int
    {
        return in_array($days, [7, 30, 90], true) ? $days : 30;
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable, 2: CarbonImmutable} [from, to, previousFrom] */
    protected function window(int $days): array
    {
        $to = CarbonImmutable::now()->endOfDay();
        $from = $to->subDays($days - 1)->startOfDay();
        $previousFrom = $from->subDays($days);

        return [$from, $to, $previousFrom];
    }

    protected function totals(callable $scoped, CarbonImmutable $from, CarbonImmutable $to, CarbonImmutable $previousFrom): array
    {
        /** @var Builder $current */
        $current = $scoped()->whereBetween('ce.created_at', [$from, $to]);
        $row = $current->selectRaw('count(*) as clicks, count(distinct ce.unique_key) as unique_clicks')->first();

        $previous = (int) $scoped()->whereBetween('ce.created_at', [$previousFrom, $from->subSecond()])->count();
        $allTime = (int) $scoped()->count();

        $clicks = (int) ($row->clicks ?? 0);

        return [
            'clicks' => $clicks,
            'unique_clicks' => (int) ($row->unique_clicks ?? 0),
            'previous_clicks' => $previous,
            'change_pct' => $previous > 0 ? round((($clicks - $previous) / $previous) * 100, 1) : null,
            'all_time_clicks' => $allTime,
        ];
    }

    /** One point per day, zero-filled. */
    protected function dailySeries(callable $scoped, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rows = $scoped()
            ->whereBetween('ce.created_at', [$from, $to])
            ->selectRaw("to_char(date_trunc('day', ce.created_at), 'YYYY-MM-DD') as day, count(*) as clicks, count(distinct ce.unique_key) as unique_clicks")
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $series = [];
        for ($d = $from; $d->lte($to); $d = $d->addDay()) {
            $key = $d->format('Y-m-d');
            $series[] = [
                'date' => $key,
                'clicks' => (int) ($rows[$key]->clicks ?? 0),
                'unique_clicks' => (int) ($rows[$key]->unique_clicks ?? 0),
            ];
        }

        return $series;
    }

    protected function sources(callable $scoped, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rows = $scoped()
            ->whereBetween('ce.created_at', [$from, $to])
            ->selectRaw('ce.referrer, count(*) as clicks')
            ->groupBy('ce.referrer')
            ->get();

        $byHost = [];
        foreach ($rows as $r) {
            $host = $r->referrer ? (parse_url((string) $r->referrer, PHP_URL_HOST) ?: 'Other') : 'Direct';
            $host = Str::startsWith($host, 'www.') ? substr($host, 4) : $host;
            $byHost[$host] = ($byHost[$host] ?? 0) + (int) $r->clicks;
        }
        arsort($byHost);

        $out = [];
        foreach (array_slice($byHost, 0, 6, true) as $source => $clicks) {
            $out[] = ['source' => $source, 'clicks' => $clicks];
        }

        return $out;
    }

    protected function devices(callable $scoped, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rows = $scoped()
            ->whereBetween('ce.created_at', [$from, $to])
            ->selectRaw('ce.user_agent, count(*) as clicks')
            ->groupBy('ce.user_agent')
            ->get();

        $buckets = ['mobile' => 0, 'desktop' => 0, 'other' => 0];
        foreach ($rows as $r) {
            $ua = Str::lower((string) $r->user_agent);
            $kind = match (true) {
                $ua === '' => 'other',
                Str::contains($ua, ['iphone', 'android', 'mobi', 'ipad']) => 'mobile',
                Str::contains($ua, ['windows', 'macintosh', 'linux', 'x11']) => 'desktop',
                default => 'other',
            };
            $buckets[$kind] += (int) $r->clicks;
        }

        return [
            ['device' => 'Mobile', 'clicks' => $buckets['mobile']],
            ['device' => 'Desktop', 'clicks' => $buckets['desktop']],
            ['device' => 'Other', 'clicks' => $buckets['other']],
        ];
    }

    protected function trackingUrl(?string $code): ?string
    {
        return $code ? url("/api/v1/t/{$code}") : null;
    }
}
