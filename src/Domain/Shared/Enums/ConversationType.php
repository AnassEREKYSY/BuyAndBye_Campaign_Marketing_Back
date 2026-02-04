<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum ConversationType: string
{
    case Direct = 'Direct';
    case Group = 'Group';
}
