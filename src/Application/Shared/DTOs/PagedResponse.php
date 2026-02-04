<?php

declare(strict_types=1);

namespace Src\Application\Shared\DTOs;

class PagedResponse
{
    /**
     * @param array $items
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $pageSize,
        public int $total
    ) {
    }
}
