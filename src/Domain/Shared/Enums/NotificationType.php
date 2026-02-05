<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum NotificationType: string
{
    case System = 'System';
    case Order = 'Order';
    case Message = 'Message';
    case Promotion = 'Promotion';
}
