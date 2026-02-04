<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum ReportReason: string
{
    case Fraud = 'Fraud';
    case Counterfeit = 'Counterfeit';
    case Harassment = 'Harassment';
    case Spam = 'Spam';
    case Other = 'Other';
}
