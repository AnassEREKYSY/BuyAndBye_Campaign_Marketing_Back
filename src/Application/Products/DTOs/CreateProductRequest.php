<?php

declare(strict_types=1);

namespace Src\Application\Products\DTOs;

class CreateProductRequest
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $categoryId,
        public string $condition,
        public float $price,
        public int $stockQuantity,
        public ?array $images,
        public ?array $tags,
        public ?float $weightKg,
        public ?string $sku,
        public bool $isDigital,
        public bool $allowReturns,
        public int $returnDays
    ) {
    }
}
