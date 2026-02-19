<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'display_name' => $this->display_name,
            'photo_url' => $this->photo_url,
            'role' => $this->role->value,
            'status' => $this->status->value,
            'profile_completed' => (bool) $this->profile_completed,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            'brand_profile' => $this->brandProfile ? [
                'brand_name' => $this->brandProfile->brand_name,
                'website_url' => $this->brandProfile->website_url,
                'industry' => $this->brandProfile->industry,
                'contact_email' => $this->brandProfile->contact_email,
                'contact_phone' => $this->brandProfile->contact_phone,
                'description' => $this->brandProfile->description,
                'logo_url' => $this->brandProfile->logo_url,
            ] : null,

            'influencer_profile' => $this->influencerProfile ? [
                'niche' => $this->influencerProfile->niche,
                'instagram_url' => $this->influencerProfile->instagram_url,
                'tiktok_url' => $this->influencerProfile->tiktok_url,
                'youtube_url' => $this->influencerProfile->youtube_url,
                'followers_instagram' => $this->influencerProfile->followers_instagram,
                'followers_tiktok' => $this->influencerProfile->followers_tiktok,
                'followers_youtube' => $this->influencerProfile->followers_youtube,
                'avg_engagement_rate' => $this->influencerProfile->avg_engagement_rate,
                'country_code' => $this->influencerProfile->country_code,
                'language' => $this->influencerProfile->language,
                'media_kit_url' => $this->influencerProfile->media_kit_url,
            ] : null,
        ];
    }
}