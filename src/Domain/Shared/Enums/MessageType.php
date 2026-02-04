<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum MessageType: string
{
    case Text = 'Text';
    case Image = 'Image';
    case System = 'System';
}
