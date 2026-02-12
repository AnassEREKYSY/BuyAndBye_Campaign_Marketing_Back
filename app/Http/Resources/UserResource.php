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
            'profile_skipped' => (bool) $this->profile_skipped,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            'profile' => $this->profile ? [
                'phone_number' => $this->profile->phone_number,
                'birth_date' => $this->profile->birth_date?->toDateString(),
                'gender' => $this->profile->gender,
                'country_code' => $this->profile->country_code,
                'locale' => $this->profile->locale,
                'buyer_categories' => $this->profile->buyer_categories,
                'buyer_interests' => $this->profile->buyer_interests,
                'payment_methods' => $this->profile->payment_methods,
            ] : null,

            'seller_profile' => $this->sellerProfile ? [
                'store_name' => $this->sellerProfile->store_name,
                'company_name' => $this->sellerProfile->company_name,
                'vat_number' => $this->sellerProfile->vat_number,
                'support_email' => $this->sellerProfile->support_email,
                'support_phone' => $this->sellerProfile->support_phone,
                'category_tags' => $this->sellerProfile->category_tags,
                'store_description' => $this->sellerProfile->store_description,
                'store_banner_url' => $this->sellerProfile->store_banner_url,
            ] : null,
        ];
    }
}
