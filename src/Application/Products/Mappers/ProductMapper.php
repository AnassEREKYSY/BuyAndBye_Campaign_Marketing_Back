<?php

declare(strict_types=1);

namespace Src\Application\Products\Mappers;

use Src\Application\Products\DTOs\ProductResponse;
use Src\Domain\Products\Entities\Product;

class ProductMapper
{
    public static function toProductResponse(Product $product): ProductResponse
    {
        return new ProductResponse(
            id: $product->id,
            sellerId: $product->sellerId,
            title: $product->title,
            description: $product->description,
            categoryId: $product->categoryId,
            condition: $product->condition,
            status: $product->status,
            price: $product->price,
            compareAtPrice: $product->compareAtPrice,
            stockQuantity: $product->stockQuantity,
            images: $product->images,
            tags: $product->tags,
            weightKg: $product->weightKg,
            sku: $product->sku,
            isDigital: $product->isDigital,
            allowReturns: $product->allowReturns,
            returnDays: $product->returnDays,
            isFeatured: $product->isFeatured,
            publishedAt: $product->publishedAt?->format(DATE_ATOM)
        );
    }
}
