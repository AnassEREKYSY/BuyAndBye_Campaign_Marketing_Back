<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Enums;

enum SellerVerificationStatus: string
{
    case NotSubmitted = 'NotSubmitted';
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
}
