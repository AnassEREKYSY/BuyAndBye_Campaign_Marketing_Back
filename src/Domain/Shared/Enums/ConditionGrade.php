<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum ConditionGrade: string
{
    case New = 'New';
    case LikeNew = 'LikeNew';
    case VeryGood = 'VeryGood';
    case Good = 'Good';
    case Acceptable = 'Acceptable';
}
