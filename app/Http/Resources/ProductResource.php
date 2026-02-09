<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        return [
            'id' => $product->id,
            'sellerId' => $product->seller_id,
            'title' => $product->title,
            'description' => $product->description,
            'categoryId' => $product->category_id,
            'condition' => $product->condition,
            'status' => $product->status,
            'price' => (float) $product->price,
            'compareAtPrice' => $product->compare_at_price ? (float) $product->compare_at_price : null,
            'stockQuantity' => (int) $product->stock_quantity,
            'images' => $product->images,
            'tags' => $product->tags,
            'weightKg' => $product->weight_kg ? (float) $product->weight_kg : null,
            'sku' => $product->sku,
            'isDigital' => (bool) $product->is_digital,
            'allowReturns' => (bool) $product->allow_returns,
            'returnDays' => (int) $product->return_days,
            'isFeatured' => (bool) $product->is_featured,
            'publishedAt' => $product->published_at?->toIso8601String(),
        ];
    }
}
