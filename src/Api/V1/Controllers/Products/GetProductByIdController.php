<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Resources\Products\ProductResource;
use Src\Application\Products\Mappers\ProductMapper;
use Src\Application\Products\UseCases\GetProductByIdUseCase;

/**
 * @OA\Get(
 *     path="/api/v1/products/get-one/{productId}",
 *     tags={"Products"},
 *     summary="Get product by ID",
 *     description="Retrieve a product by its ID",
 *
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Product retrieved successfully"
 *     ),
 *     @OA\Response(response=404, description="Product not found")
 * )
 */

 class GetProductByIdController extends Controller
 {
     public function __invoke(
         string $productId,
         GetProductByIdUseCase $useCase
     ): ProductResource {
         $product = $useCase->execute($productId);
         $this->authorize('view', $product);
         return new ProductResource(
             ProductMapper::toProductResponse($product)
         );
     }
 }
