<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Application\Dtos\Product\CreateProductDTO;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Support\Str;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository,
        private readonly FileStorageInterface $fileStorage,
    ) {}

    public function execute(CreateProductDTO $dto): Product
    {
        $product = $this->repository->create([
            'seller_id' => $dto->sellerId,
            'title' => $dto->title,
            'description' => $dto->description,
            'category_id' => $dto->categoryId,
            'condition' => $dto->condition,
            'status' => ProductStatus::Draft->value,
            'price' => $dto->price,
            'stock_quantity' => $dto->stockQuantity,
            'tags' => $dto->tags,
            'weight_kg' => $dto->weightKg,
            'sku' => $dto->sku,
            'is_digital' => $dto->isDigital,
            'allow_returns' => $dto->allowReturns,
            'return_days' => $dto->returnDays,
        ]);

        $imageUrls = [];

        if ($dto->images) {
            foreach ($dto->images as $image) {
                $imageUrls[] = $this->fileStorage->storeProductImage(
                    $dto->sellerId,
                    $product->id,
                    $image
                );
            }

            $this->repository->update($product, [
                'images' => $imageUrls
            ]);
        }

        return $product->fresh();
    }
}