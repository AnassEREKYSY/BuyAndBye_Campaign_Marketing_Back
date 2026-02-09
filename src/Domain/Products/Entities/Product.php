<?php

declare(strict_types=1);

namespace Src\Domain\Products\Entities;

use DateTimeImmutable;

class Product
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
        public ?float $compareAtPrice = null,
        public int $stockQuantity = 0,
        public ?array $images = null,
        public ?array $tags = null,
        public ?float $weightKg = null,
        public ?string $sku = null,
        public bool $isDigital = false,
        public bool $allowReturns = true,
        public int $returnDays = 30,
        public bool $isFeatured = false,
        public ?DateTimeImmutable $publishedAt = null
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function sellerId(): string
    {
        return $this->sellerId;
    }
}
