<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $products
    ) {}

    public function execute(User $brand, string $productId): void
    {
        $product = $this->products->findById($productId);
        if (! $product) {
            throw new NotFoundHttpException('Product not found.');
        }

        if ($product->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $this->products->delete($product);
    }
}