<?php

declare(strict_types=1);

namespace App\Application\UseCases\Payouts;

use App\Domain\Contracts\CollaborationPayoutRepositoryInterface;
use App\Enums\PayoutStatus;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MarkPayoutPaidUseCase
{
    public function __construct(
        private readonly CollaborationPayoutRepositoryInterface $payouts
    ) {}

    public function execute(string $payoutId)
    {
        $payout = $this->payouts->findById($payoutId);
        if (! $payout) {
            throw new NotFoundHttpException('Payout not found.');
        }

        if ($payout->status !== PayoutStatus::Approved) {
            throw new ConflictHttpException('Only approved payouts can be marked as paid.');
        }

        $this->payouts->updateStatus($payout, PayoutStatus::Paid);

        return $payout->fresh(['collaboration', 'tier']);
    }
}