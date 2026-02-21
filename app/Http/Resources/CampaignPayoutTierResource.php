<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignPayoutTierResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'metric' => $this->metric,
            'from_value' => (int) $this->from_value,
            'to_value' => $this->to_value !== null ? (int) $this->to_value : null,
            'payout_amount' => (float) $this->payout_amount,
            'currency' => $this->currency,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}