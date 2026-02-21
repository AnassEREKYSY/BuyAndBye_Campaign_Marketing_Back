<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollaborationResource extends JsonResource
{
    public function toArray($request): array
    {
        $trackingCode = $this->trackingLink?->code;
        $trackingUrl = $trackingCode ? url("/api/v1/t/{$trackingCode}") : null;

        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'brand_id' => $this->brand_id,
            'influencer_id' => $this->influencer_id,
            'accepted_at' => $this->accepted_at?->toIso8601String(),
            'status' => $this->status->value,

            'tracking' => [
                'code' => $this->trackingLink?->code,
                'url' => $trackingUrl,
                'destination_url' => $this->trackingLink?->destination_url,
            ],

            'promo' => [
                'code' => $this->promoCode?->code,
            ],

            'campaign' => $this->whenLoaded('campaign', fn () => [
                'id' => $this->campaign->id,
                'title' => $this->campaign->title,
                'status' => $this->campaign->status->value,
                'commission_type' => $this->campaign->commission_type->value,
                'commission_value' => (float) $this->campaign->commission_value,
                'budget' => $this->campaign->budget !== null ? (float) $this->campaign->budget : null,
                'start_at' => $this->campaign->start_at?->toIso8601String(),
                'end_at' => $this->campaign->end_at?->toIso8601String(),
                'product' => $this->campaign->relationLoaded('product') && $this->campaign->product
                    ? [
                        'id' => $this->campaign->product->id,
                        'name' => $this->campaign->product->name,
                        'landing_url' => $this->campaign->product->landing_url,
                      ]
                    : null,
            ]),

            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'display_name' => $this->brand->display_name,
                'photo_url' => $this->brand->photo_url,
            ]),

            'influencer' => $this->whenLoaded('influencer', fn () => [
                'id' => $this->influencer->id,
                'display_name' => $this->influencer->display_name,
                'photo_url' => $this->influencer->photo_url,
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}