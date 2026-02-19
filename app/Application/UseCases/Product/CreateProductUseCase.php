<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Application\Dtos\Product\CreateProductDTO;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $products
    ) {}

    public function execute(User $brand, CreateProductDTO $dto): Product
    {
        return $this->products->create([
            'brand_id' => $brand->id,
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'currency' => $dto->currency ?? 'MAD',
            'landing_url' => $dto->landingUrl,
            'status' => ProductStatus::Active->value,
            'images' => $dto->images ?? [],
        ]);
    }
}