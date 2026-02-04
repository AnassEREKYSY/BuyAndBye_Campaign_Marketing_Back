<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Application\Products\UseCases\DeleteProductUseCase;
use Src\Application\Products\UseCases\GetProductByIdUseCase;

class DeleteProductController extends Controller
{
    public function __invoke(
        string $productId,
        GetProductByIdUseCase $getById,
        DeleteProductUseCase $useCase
    ): JsonResponse {
        $product = $getById->execute($productId);
        $this->authorize('delete', $product);

        $useCase->execute($productId);

        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}
