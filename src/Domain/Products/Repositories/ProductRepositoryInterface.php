<?php

declare(strict_types=1);

namespace Src\Domain\Products\Repositories;

use Src\Domain\Products\Entities\Product;

interface ProductRepositoryInterface
{
    public function create(array $attributes): Product;

    public function update(string $id, array $attributes): Product;

    public function findById(string $id): ?Product;

    /**
     * @return array{items: Product[], total: int}
     */
    public function paginateBySellerId(string $sellerId, int $page, int $pageSize): array;

    public function softDelete(string $id): void;
}
