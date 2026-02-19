<?php

declare(strict_types=1);

namespace App\Application\Dtos\Campaign;

final class CreateCampaignDTO
{
    public function __construct(
        public readonly string $productId,
        public readonly string $title,
        public readonly ?string $objective,
        public readonly string $commissionType,
        public readonly float $commissionValue,
        public readonly ?float $budget,
        public readonly ?string $startAt,
        public readonly ?string $endAt,
    ) {}
}