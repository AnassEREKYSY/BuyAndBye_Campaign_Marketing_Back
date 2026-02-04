<?php

declare(strict_types=1);

namespace Src\Application\Products\UseCases;

use Src\Domain\Products\Exceptions\ProductNotFoundException;
use Src\Domain\Products\Repositories\ProductRepositoryInterface;

class DeleteProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $products
    ) {
    }

    public function execute(string $productId): void
    {
        $existing = $this->products->findById($productId);
        if (!$existing) {
            throw ProductNotFoundException::forId($productId);
        }

        $this->products->softDelete($productId);
    }
}
