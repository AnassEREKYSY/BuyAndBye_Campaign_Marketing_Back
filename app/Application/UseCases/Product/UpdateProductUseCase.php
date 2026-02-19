<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Application\Dtos\Product\UpdateProductDTO;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $products
    ) {}

    public function execute(User $brand, string $productId, UpdateProductDTO $dto): Product
    {
        $product = $this->products->findById($productId);
        if (! $product) {
            throw new NotFoundHttpException('Product not found.');
        }

        if ($product->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $data = array_filter([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'currency' => $dto->currency,
            'landing_url' => $dto->landingUrl,
            'images' => $dto->images,
            'status' => $dto->status,
        ], fn ($v) => $v !== null);

        if (! empty($data)) {
            $this->products->update($product, $data);
        }

        return $product->fresh();
    }
}