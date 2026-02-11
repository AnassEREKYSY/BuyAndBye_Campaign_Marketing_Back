<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Application\Dtos\Product\UpdateProductDTO;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    public function execute(Product $product, UpdateProductDTO $dto): void
    {
        $updateData = array_filter([
            'title' => $dto->title,
            'description' => $dto->description,
            'category_id' => $dto->categoryId,
            'condition' => $dto->condition,
            'price' => $dto->price,
            'stock_quantity' => $dto->stockQuantity,
            'images' => $dto->images,
            'tags' => $dto->tags,
            'weight_kg' => $dto->weightKg,
            'sku' => $dto->sku,
            'is_digital' => $dto->isDigital,
            'allow_returns' => $dto->allowReturns,
            'return_days' => $dto->returnDays,
        ], fn ($value) => $value !== null);

        if (! empty($updateData)) {
            $this->repository->update($product, $updateData);
        }
    }
}
