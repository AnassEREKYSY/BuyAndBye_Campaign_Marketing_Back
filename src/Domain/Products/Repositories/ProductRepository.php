<?php

declare(strict_types=1);

namespace Src\Domain\Products\Repositories;

use Src\Domain\Products\Entities\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function create(array $attributes): Product
    {
