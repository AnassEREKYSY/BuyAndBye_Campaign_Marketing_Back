<?php

declare(strict_types=1);

namespace App\Application\UseCases\Payouts;

use App\Domain\Contracts\CollaborationPayoutRepositoryInterface;
use App\Enums\PayoutStatus;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApprovePayoutUseCase
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

        if ($payout->status !== PayoutStatus::Pending) {
            throw new ConflictHttpException('Only pending payouts can be approved.');
        }

        $this->payouts->updateStatus($payout, PayoutStatus::Approved);

        return $payout->fresh(['collaboration', 'tier']);
    }
}