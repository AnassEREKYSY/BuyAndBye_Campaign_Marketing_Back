<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Application\Dtos\Product\CreateProductDTO;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Enums\ProductStatus;
use App\Models\Product;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    public function execute(CreateProductDTO $dto): Product
    {
        return $this->repository->create([
            'seller_id' => $dto->sellerId,
            'title' => $dto->title,
            'description' => $dto->description,
            'category_id' => $dto->categoryId,
            'condition' => $dto->condition,
            'status' => ProductStatus::Draft->value,
            'price' => $dto->price,
            'stock_quantity' => $dto->stockQuantity,
            'images' => $dto->images,
            'tags' => $dto->tags,
            'weight_kg' => $dto->weightKg,
            'sku' => $dto->sku,
            'is_digital' => $dto->isDigital,
            'allow_returns' => $dto->allowReturns,
            'return_days' => $dto->returnDays,
        ]);
    }
}
