<?php

declare(strict_types=1);

namespace App\Application\UseCases\Campaign;

use App\Application\Dtos\Campaign\UpdateCampaignDTO;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateCampaignUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns
    ) {}

    public function execute(User $brand, string $campaignId, UpdateCampaignDTO $dto)
    {
        $campaign = $this->campaigns->findById($campaignId);
        if (! $campaign) {
            throw new NotFoundHttpException('Campaign not found.');
        }

        if ($campaign->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $data = array_filter([
            'title' => $dto->title,
            'objective' => $dto->objective,
            'commission_type' => $dto->commissionType,
            'commission_value' => $dto->commissionValue,
            'budget' => $dto->budget,
            'start_at' => $dto->startAt,
            'end_at' => $dto->endAt,
            'status' => $dto->status,
        ], fn ($v) => $v !== null);

        if (! empty($data)) {
            $this->campaigns->update($campaign, $data);
        }

        return $campaign->fresh(['product', 'brand']);
    }
}