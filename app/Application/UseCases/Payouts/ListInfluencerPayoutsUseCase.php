<?php

declare(strict_types=1);

namespace App\Application\UseCases\Payouts;

use App\Domain\Contracts\CollaborationPayoutRepositoryInterface;
use App\Models\User;

class ListInfluencerPayoutsUseCase
{
    public function __construct(
        private readonly CollaborationPayoutRepositoryInterface $payouts
    ) {}

    public function execute(User $influencer)
    {
        return $this->payouts->listForInfluencer($influencer->id);
    }
}