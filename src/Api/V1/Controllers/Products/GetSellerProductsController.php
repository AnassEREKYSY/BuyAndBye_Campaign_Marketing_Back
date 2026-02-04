<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Resources\Products\ProductsResource;
use Illuminate\Support\Facades\Gate;
use Src\Application\Products\UseCases\GetSellerProductsUseCase;

class GetSellerProductsController extends Controller
{
    public function __invoke(GetSellerProductsUseCase $useCase): ProductsResource
    {
        Gate::authorize('seller-or-admin');

        $page = (int) request()->query('page', 1);
        $pageSize = (int) request()->query('pageSize', 20);
        $paged = $useCase->execute($page, $pageSize);

        return new ProductsResource($paged);
    }
}
