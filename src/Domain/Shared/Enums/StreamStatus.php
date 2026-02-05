<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum StreamStatus: string
{
    case Scheduled = 'Scheduled';
    case Live = 'Live';
    case Ended = 'Ended';
    case Cancelled = 'Cancelled';
}
