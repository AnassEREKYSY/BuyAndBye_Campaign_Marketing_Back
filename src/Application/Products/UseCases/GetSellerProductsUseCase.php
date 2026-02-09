<?php

declare(strict_types=1);

namespace Src\Application\Products\UseCases;

use Src\Application\Products\Mappers\ProductMapper;
use Src\Application\Products\DTOs\PagedResponse;
use Src\Domain\Products\Repositories\ProductRepositoryInterface;
use Src\Domain\Users\Services\UserContextInterface;

class GetSellerProductsUseCase
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private UserContextInterface $userContext
    ) {
    }

    public function execute(int $page, int $pageSize): PagedResponse
    {
        $sellerId = $this->userContext->getUserId();
        $result = $this->products->paginateBySellerId($sellerId, $page, $pageSize);
        $items = array_map([ProductMapper::class, 'toProductResponse'], $result['items']);

        return new PagedResponse(
            items: $items,
            page: $page,
            pageSize: $pageSize,
            total: $result['total']
        );
    }
}
