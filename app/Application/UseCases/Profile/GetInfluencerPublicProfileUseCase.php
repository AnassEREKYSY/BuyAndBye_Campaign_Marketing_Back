<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Domain\Contracts\InfluencerProfileRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetInfluencerPublicProfileUseCase
{
    public function __construct(
        private readonly InfluencerProfileRepositoryInterface $profiles
    ) {}

    public function execute(string $influencerId)
    {
        $profile = $this->profiles->findByUserId($influencerId);

        if (! $profile) {
            throw new NotFoundHttpException('Influencer profile not found.');
        }

        return $profile;
    }
}