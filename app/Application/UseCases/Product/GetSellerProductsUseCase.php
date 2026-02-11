<?php

declare(strict_types=1);

namespace App\Application\UseCases\Product;

use App\Domain\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetSellerProductsUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    public function execute(string $sellerId, int $page, int $pageSize): LengthAwarePaginator
    {
        return $this->repository->paginateBySeller($sellerId, $page, $pageSize);
    }
}
