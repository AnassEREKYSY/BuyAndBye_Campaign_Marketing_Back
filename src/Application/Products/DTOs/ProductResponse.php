<?php

declare(strict_types=1);

namespace Src\Application\Products\DTOs;

class ProductResponse
{
    public function __construct(
        public string $id,
        public string $sellerId,
        public string $title,
        public ?string $description,
        public ?string $categoryId,
        public string $condition,
        public string $status,
        public float $price,
        public ?float $compareAtPrice,
        public int $stockQuantity,
        public ?array $images,
        public ?array $tags,
        public ?float $weightKg,
        public ?string $sku,
        public bool $isDigital,
        public bool $allowReturns,
        public int $returnDays,
        public bool $isFeatured,
        public ?string $publishedAt
    ) {
    }
}
