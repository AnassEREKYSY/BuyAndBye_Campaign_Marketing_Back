<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum ProductStatus: string
{
    case Draft = 'Draft';
    case Active = 'Active';
    case OutOfStock = 'OutOfStock';
    case Archived = 'Archived';
    case Banned = 'Banned';
}
