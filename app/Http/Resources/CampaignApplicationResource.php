<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignApplicationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'influencer_id' => $this->influencer_id,
            'message' => $this->message,
            'status' => $this->status->value,
            'campaign' => $this->whenLoaded('campaign', fn () => new CampaignResource($this->campaign)),
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