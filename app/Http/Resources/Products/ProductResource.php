<?php

declare(strict_types=1);

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Application\Products\DTOs\ProductResponse;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        /** @var ProductResponse $product */
        $product = $this->resource;

        return [
            'id' => $product->id,
            'sellerId' => $product->sellerId,
            'title' => $product->title,
            'description' => $product->description,
            'categoryId' => $product->categoryId,
            'condition' => $product->condition,
            'status' => $product->status,
            'price' => $product->price,
            'compareAtPrice' => $product->compareAtPrice,
            'stockQuantity' => $product->stockQuantity,
            'images' => $product->images,
            'tags' => $product->tags,
            'weightKg' => $product->weightKg,
            'sku' => $product->sku,
            'isDigital' => $product->isDigital,
            'allowReturns' => $product->allowReturns,
            'returnDays' => $product->returnDays,
            'isFeatured' => $product->isFeatured,
            'publishedAt' => $product->publishedAt,
        ];
    }
}
