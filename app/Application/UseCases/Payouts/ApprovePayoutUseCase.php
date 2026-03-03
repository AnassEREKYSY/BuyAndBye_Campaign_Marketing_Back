<?php

declare(strict_types=1);

namespace App\Application\UseCases\Payouts;

use App\Domain\Contracts\CollaborationPayoutRepositoryInterface;
use App\Domain\Contracts\NotificationServiceInterface;
use App\Enums\NotificationType;
use App\Enums\PayoutStatus;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApprovePayoutUseCase
{
    public function __construct(
        private readonly CollaborationPayoutRepositoryInterface $payouts,
        private readonly NotificationServiceInterface $notifications,
    ) {}

    public function execute(string $payoutId)
    {
        $payout = $this->payouts->findById($payoutId);
        if (! $payout) {
            throw new NotFoundHttpException('Payout not found.');
        }

        if ($payout->status !== PayoutStatus::Pending) {
            throw new ConflictHttpException('Only pending payouts can be approved.');
        }

        $this->payouts->updateStatus($payout, PayoutStatus::Approved);

        $fresh = $payout->fresh(['collaboration', 'tier']);
        $collab = $fresh->collaboration;

        $this->notifications->notify(
            userId: (string) $collab->influencer_id,
            type: NotificationType::PayoutApproved->value,
            title: 'Payout approved',
            body: 'Your payout was approved. Amount: ' . $fresh->amount . ' ' . $fresh->currency,
            data: [
                'payout_id' => (string) $fresh->id,
                'collaboration_id' => (string) $collab->id,
            ],
            actorId: null,
            entityType: 'CollaborationPayout',
            entityId: (string) $fresh->id
        );

        $this->notifications->notify(
            userId: (string) $collab->brand_id,
            type: NotificationType::PayoutApproved->value,
            title: 'Payout approved',
            body: 'A payout was approved. Amount: ' . $fresh->amount . ' ' . $fresh->currency,
            data: [
                'payout_id' => (string) $fresh->id,
                'collaboration_id' => (string) $collab->id,
            ],
            actorId: null,
            entityType: 'CollaborationPayout',
            entityId: (string) $fresh->id
        );

        return $fresh;
    }
}