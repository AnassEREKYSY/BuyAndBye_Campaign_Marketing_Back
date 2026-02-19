<?php

declare(strict_types=1);

namespace App\Application\Dtos\Product;

final class UpdateProductDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?float $price,
        public readonly ?string $currency,
        public readonly ?string $landingUrl,
        public readonly ?array $images,
        public readonly ?string $status,
    ) {}
}