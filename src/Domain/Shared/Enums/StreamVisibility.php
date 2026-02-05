<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum StreamVisibility: string
{
    case Public = 'Public';
    case Unlisted = 'Unlisted';
    case Private = 'Private';
}
