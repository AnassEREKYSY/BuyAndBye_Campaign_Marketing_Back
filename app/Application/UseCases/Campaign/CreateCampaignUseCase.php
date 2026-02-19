<?php

declare(strict_types=1);

namespace App\Application\UseCases\Campaign;

use App\Application\Dtos\Campaign\CreateCampaignDTO;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\ForbiddenHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateCampaignUseCase
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaigns,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function execute(User $brand, CreateCampaignDTO $dto): Campaign
    {
        $product = $this->products->findById($dto->productId);
        if (! $product) {
            throw new NotFoundHttpException('Product not found.');
        }

        if ($product->brand_id !== $brand->id) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        return $this->campaigns->create([
            'brand_id' => $brand->id,
            'product_id' => $product->id,
            'title' => $dto->title,
            'objective' => $dto->objective,
            'commission_type' => $dto->commissionType,
            'commission_value' => $dto->commissionValue,
            'budget' => $dto->budget,
            'start_at' => $dto->startAt,
            'end_at' => $dto->endAt,
            'status' => CampaignStatus::Draft->value,
        ]);
    }
}