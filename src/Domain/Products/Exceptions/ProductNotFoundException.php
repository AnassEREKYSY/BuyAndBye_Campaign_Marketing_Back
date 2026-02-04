<?php

declare(strict_types=1);

namespace Src\Domain\Products\Exceptions;

use RuntimeException;

class ProductNotFoundException extends RuntimeException
{
    public static function forId(string $id): self
    {
        return new self("Product {$id} not found.");
    }
}
