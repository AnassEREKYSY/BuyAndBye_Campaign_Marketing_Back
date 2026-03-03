<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationType: string
{
    case CampaignPublished = 'campaign.published';

    case CampaignApplied = 'campaign.applied';
    case ApplicationShortlisted = 'application.shortlisted';
    case ApplicationAccepted = 'application.accepted';
    case ApplicationRejected = 'application.rejected';

    case PayoutClosed = 'payout.closed';
    case PayoutApproved = 'payout.approved';
    case PayoutPaid = 'payout.paid';
}