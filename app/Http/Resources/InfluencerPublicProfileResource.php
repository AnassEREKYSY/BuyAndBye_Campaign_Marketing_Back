<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InfluencerPublicProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->user?->id ?? $this->user_id,

            'display_name' => $this->user?->display_name,
            'photo_url' => $this->user?->photo_url,

            'niche' => $this->niche ?? null,
            'country_code' => $this->country_code ?? null,
            'language' => $this->language ?? null,

            'instagram_url' => $this->instagram_url ?? null,
            'tiktok_url' => $this->tiktok_url ?? null,
            'youtube_url' => $this->youtube_url ?? null,
            'media_kit_url' => $this->media_kit_url ?? null,

            'followers_instagram' => $this->followers_instagram ?? null,
            'followers_tiktok' => $this->followers_tiktok ?? null,
            'followers_youtube' => $this->followers_youtube ?? null,
            'avg_engagement_rate' => $this->avg_engagement_rate ?? null,

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}