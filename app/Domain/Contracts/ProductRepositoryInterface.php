<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginateBySeller(string $sellerId, int $page, int $pageSize): LengthAwarePaginator;

    public function create(array $data): Product;

    public function update(Product $product, array $data): void;

    public function delete(Product $product): void;
}
