<?php

declare(strict_types=1);

namespace Src\Application\Products\DTOs;

class UpdateProductRequest
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $categoryId = null,
        public ?string $condition = null,
        public ?float $price = null,
        public ?int $stockQuantity = null,
        public ?array $images = null,
        public ?array $tags = null,
        public ?float $weightKg = null,
        public ?string $sku = null,
        public ?bool $isDigital = null,
        public ?bool $allowReturns = null,
        public ?int $returnDays = null
    ) {
    }
}
