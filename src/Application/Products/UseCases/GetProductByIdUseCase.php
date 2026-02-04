<?php

declare(strict_types=1);

namespace Src\Application\Products\UseCases;

use Src\Application\Products\DTOs\ProductResponse;
use Src\Application\Products\Mappers\ProductMapper;
use Src\Domain\Products\Exceptions\ProductNotFoundException;
use Src\Domain\Products\Repositories\ProductRepositoryInterface;

class GetProductByIdUseCase
{
    public function __construct(
        private ProductRepositoryInterface $products
    ) {
    }

    public function execute(string $productId): ProductResponse
    {
        $product = $this->products->findById($productId);

        if (!$product) {
            throw ProductNotFoundException::forId($productId);
        }

        return ProductMapper::toProductResponse($product);
    }
}
