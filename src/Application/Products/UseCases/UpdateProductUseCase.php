<?php

declare(strict_types=1);

namespace Src\Application\Products\UseCases;

use Src\Application\Products\DTOs\ProductResponse;
use Src\Application\Products\DTOs\UpdateProductRequest;
use Src\Application\Products\Mappers\ProductMapper;
use Src\Domain\Products\Exceptions\ProductNotFoundException;
use Src\Domain\Products\Repositories\ProductRepositoryInterface;

class UpdateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $products
    ) {
    }

    public function execute(string $productId, UpdateProductRequest $request): ProductResponse
    {
        $existing = $this->products->findById($productId);
        if (!$existing) {
            throw ProductNotFoundException::forId($productId);
        }

        $product = $this->products->update($productId, array_filter([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->categoryId,
            'condition' => $request->condition,
            'price' => $request->price,
            'stock_quantity' => $request->stockQuantity,
            'images' => $request->images,
            'tags' => $request->tags,
            'weight_kg' => $request->weightKg,
            'sku' => $request->sku,
            'is_digital' => $request->isDigital,
            'allow_returns' => $request->allowReturns,
            'return_days' => $request->returnDays,
        ], static fn ($v) => $v !== null));
        

        return ProductMapper::toProductResponse($product);
    }
}
