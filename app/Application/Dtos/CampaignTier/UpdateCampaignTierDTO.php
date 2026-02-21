<?php

declare(strict_types=1);

namespace App\Application\Dtos\CampaignTier;

final class UpdateCampaignTierDTO
{
    public function __construct(
        public readonly ?string $metric,
        public readonly ?int $fromValue,
        public readonly ?int $toValue,
        public readonly ?float $payoutAmount,
        public readonly ?string $currency,
    ) {}
}