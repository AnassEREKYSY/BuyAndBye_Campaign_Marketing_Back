<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Domain\Contracts\ProductRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListBrandProductsUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $products
    ) {}

    public function execute(User $brand, int $page, int $size): LengthAwarePaginator
    {
        return $this->products->paginateForBrand($brand, $page, $size);
    }
}