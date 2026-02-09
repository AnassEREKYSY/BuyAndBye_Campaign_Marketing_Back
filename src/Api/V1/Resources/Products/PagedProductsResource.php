<?php

declare(strict_types=1);

namespace Src\Api\V1\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Application\Products\DTOs\PagedResponse;

class PagedProductsResource extends JsonResource
{
    /**
     * @param PagedResponse $resource
     */
    public function toArray($request): array
    {
        return [
            'items' => ProductResource::collection($this->resource->items),
            'page' => $this->resource->page,
            'pageSize' => $this->resource->pageSize,
            'total' => $this->resource->total,
        ];
    }
}
