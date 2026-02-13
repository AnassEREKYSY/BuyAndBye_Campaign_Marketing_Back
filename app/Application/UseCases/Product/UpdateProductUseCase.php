<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Application\Dtos\Product\UpdateProductDTO;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository,
        private readonly FileStorageInterface $fileStorage,
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
            'tags' => $dto->tags,
            'weight_kg' => $dto->weightKg,
            'sku' => $dto->sku,
            'is_digital' => $dto->isDigital,
            'allow_returns' => $dto->allowReturns,
            'return_days' => $dto->returnDays,
        ], fn ($value) => $value !== null);

        if (!empty($updateData)) {
            $this->repository->update($product, $updateData);
        }
        if ($dto->images) {
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    $this->fileStorage->deleteByUrl($oldImage);
                }
            }
            $imageUrls = [];

            foreach ($dto->images as $image) {
                $imageUrls[] = $this->fileStorage->storeProductImage(
                    $product->seller_id,
                    $product->id,
                    $image
                );
            }
            $this->repository->update($product, [
                'images' => $imageUrls
            ]);
        }
    }
}