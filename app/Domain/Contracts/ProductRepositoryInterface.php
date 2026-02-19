<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginateForBrand(User $brand, int $page, int $size): LengthAwarePaginator;

    public function findById(string $id): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): void;

    public function delete(Product $product): void;
}