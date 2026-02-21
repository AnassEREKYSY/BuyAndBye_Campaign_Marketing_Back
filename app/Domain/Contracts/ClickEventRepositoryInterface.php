<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\ClickEvent;

interface ClickEventRepositoryInterface
{
    public function create(array $data): ClickEvent;

    public function countByTrackingLinkId(string $trackingLinkId): int;

    public function countUniqueByTrackingLinkId(string $trackingLinkId): int;

    public function countByCampaignId(string $campaignId): int;

    public function countUniqueByCampaignId(string $campaignId): int;

    public function groupCountsByTrackingLinkIds(array $trackingLinkIds): array;

    public function groupUniqueCountsByTrackingLinkIds(array $trackingLinkIds): array;

    public function timelineByCampaign(string $campaignId, string $from, string $to, string $group): array;

    public function timelineByTrackingLink(string $trackingLinkId, string $from, string $to, string $group): array;
}