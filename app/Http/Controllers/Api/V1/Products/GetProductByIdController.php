<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\ProductResource;
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
