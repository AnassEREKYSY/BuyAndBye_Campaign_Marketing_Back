<?php

declare(strict_types=1);

namespace Src\Application\Products\UseCases;

use Src\Application\Products\DTOs\CreateProductRequest;
use Src\Application\Products\DTOs\ProductResponse;
use Src\Application\Products\Mappers\ProductMapper;
use Src\Domain\Products\Repositories\ProductRepositoryInterface;
use Src\Domain\Shared\Enums\ProductStatus;
use Src\Domain\Users\Services\UserContextInterface;

class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private UserContextInterface $userContext
    ) {
    }

    public function execute(CreateProductRequest $request): ProductResponse
    {
        $sellerId = $this->userContext->getUserId();
        $product = $this->products->create([
            'seller_id' => $sellerId,
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->categoryId,
            'condition' => $request->condition,
            'status' => ProductStatus::Draft->value,
            'price' => $request->price,
            'stock_quantity' => $request->stockQuantity,
            'images' => $request->images,
            'tags' => $request->tags,
            'weight_kg' => $request->weightKg,
            'sku' => $request->sku,
            'is_digital' => $request->isDigital,
            'allow_returns' => $request->allowReturns,
            'return_days' => $request->returnDays,
        ]);

        return ProductMapper::toProductResponse($product);
    }
}
