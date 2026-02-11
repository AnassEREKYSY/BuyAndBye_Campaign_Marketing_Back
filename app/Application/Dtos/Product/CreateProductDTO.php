<?php

declare(strict_types=1);

namespace App\Application\Dtos\Product;

class CreateProductDTO
{
    public function __construct(
        public readonly string $sellerId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $categoryId,
        public readonly string $condition,
        public readonly float $price,
        public readonly int $stockQuantity,
        public readonly ?array $images,
        public readonly ?array $tags,
        public readonly ?float $weightKg,
        public readonly ?string $sku,
        public readonly bool $isDigital,
        public readonly bool $allowReturns,
        public readonly int $returnDays,
    ) {}
}
