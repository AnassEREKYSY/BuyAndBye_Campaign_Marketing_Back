<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class DeleteProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    public function execute(Product $product): void
    {
        $this->repository->delete($product);
    }
}
