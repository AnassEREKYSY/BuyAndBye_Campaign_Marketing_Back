<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Application\Dtos\Product\UpdateProductStatusDTO;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class UpdateProductStatusUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    public function execute(Product $product, UpdateProductStatusDTO $dto): void
    {
        $this->repository->update($product, [
            'status' => $dto->status,
        ]);
    }
}