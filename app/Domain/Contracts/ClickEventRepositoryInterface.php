<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\ClickEvent;

interface ClickEventRepositoryInterface
{
    public function create(array $data): ClickEvent;
    public function countByTrackingLinkId(string $trackingLinkId): int;
}