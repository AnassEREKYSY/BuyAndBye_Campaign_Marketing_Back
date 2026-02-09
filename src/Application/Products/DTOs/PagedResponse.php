<?php

declare(strict_types=1);

namespace Src\Application\Products\DTOs;

final class PagedResponse
{
    public function __construct(
        public readonly array $items,
        public readonly int $page,
        public readonly int $pageSize,
        public readonly int $total
    ) {
    }
}
