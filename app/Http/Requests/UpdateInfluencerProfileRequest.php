<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Profile\UpdateInfluencerProfileDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateInfluencerProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'niche' => ['sometimes', 'nullable', 'string', 'max:255'],
            'instagram_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'tiktok_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'youtube_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'followers_instagram' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'followers_tiktok' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'followers_youtube' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'avg_engagement_rate' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'country_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'language' => ['sometimes', 'nullable', 'string', 'max:20'],
            'media_kit_url' => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (count($this->all()) === 0) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }

    public function toDto(): UpdateInfluencerProfileDTO
    {
        return new UpdateInfluencerProfileDTO(
            niche: $this->input('niche'),
            instagramUrl: $this->input('instagram_url'),
            tiktokUrl: $this->input('tiktok_url'),
            youtubeUrl: $this->input('youtube_url'),
            followersInstagram: $this->input('followers_instagram'),
            followersTiktok: $this->input('followers_tiktok'),
            followersYoutube: $this->input('followers_youtube'),
            avgEngagementRate: $this->input('avg_engagement_rate'),
            countryCode: $this->input('country_code'),
            language: $this->input('language'),
            mediaKitUrl: $this->input('media_kit_url'),
        );
    }
}