<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'product_id' => $this->product_id,
            'title' => $this->title,
            'objective' => $this->objective,
            'commission_type' => $this->commission_type->value,
            'commission_value' => (float) $this->commission_value,
            'budget' => $this->budget !== null ? (float) $this->budget : null,
            'start_at' => $this->start_at?->toIso8601String(),
            'end_at' => $this->end_at?->toIso8601String(),
            'status' => $this->status->value,
            'product' => $this->whenLoaded('product', fn () => new ProductResource($this->product)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}