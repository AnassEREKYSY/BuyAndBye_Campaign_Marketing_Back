<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginateForBrand(User $brand, int $page, int $size): LengthAwarePaginator
    {
        return Product::query()
            ->where('brand_id', $brand->id)
            ->latest()
            ->paginate($size, ['*'], 'page', $page);
    }

    public function findById(string $id): ?Product
    {
        return Product::query()->where('id', $id)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): void
    {
        $product->update($data);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}