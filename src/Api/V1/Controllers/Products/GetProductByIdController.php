<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Resources\Products\ProductResource;
use Src\Application\Products\UseCases\GetProductByIdUseCase;

class GetProductByIdController extends Controller
{
    public function __invoke(
        string $productId,
        GetProductByIdUseCase $useCase
    ): ProductResource {
        $product = $useCase->execute($productId);
        $this->authorize('view', $product);

        return new ProductResource($product);
    }
}
