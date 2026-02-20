<?php

declare(strict_types=1);

namespace App\Application\Dtos\CampaignApplication;

final class ApplyToCampaignDTO
{
    public function __construct(
        public readonly ?string $message,
    ) {}
}