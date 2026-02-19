<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductCondition: string
{
    case New = 'new';
    case LikeNew = 'like_new';
    case VeryGood = 'very_good';
    case Good = 'good';
    case Acceptable = 'acceptable';
}