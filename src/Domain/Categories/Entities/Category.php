<?php

declare(strict_types=1);

namespace Src\Domain\Categories\Entities;

class Category
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $parentCategoryId = null,
        public int $sortOrder = 0,
        public bool $isActive = true
    ) {
    }
}
