<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum ReportStatus: string
{
    case Open = 'Open';
    case InReview = 'InReview';
    case Resolved = 'Resolved';
    case Rejected = 'Rejected';
}
