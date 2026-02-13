<?php

declare(strict_types=1);

namespace App\Application\Dtos\Product;

final class UpdateProductStatusDTO
{
    public function __construct(
        public readonly string $status,
    ) {}
}