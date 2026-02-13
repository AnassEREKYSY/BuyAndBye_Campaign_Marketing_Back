<?php

namespace App\Enums;

enum ProductCondition: string
{
    case New = 'New';
    case LikeNew = 'LikeNew';
    case VeryGood = 'VeryGood';
    case Good = 'Good';
    case Acceptable = 'Acceptable';
}