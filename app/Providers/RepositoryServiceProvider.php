<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Contracts\BrandProfileRepositoryInterface;
use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\InfluencerProfileRepositoryInterface;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\BrandProfileRepository;
use App\Infrastructure\Persistence\Eloquent\CampaignApplicationRepository;
use App\Infrastructure\Persistence\Eloquent\CampaignRepository;
use App\Infrastructure\Persistence\Eloquent\InfluencerProfileRepository;
use App\Infrastructure\Persistence\Eloquent\ProductRepository;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use App\Infrastructure\Storage\LocalFileStorage;
use Illuminate\Support\ServiceProvider;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\CollaborationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $singletons = [
        UserRepositoryInterface::class => UserRepository::class,
        BrandProfileRepositoryInterface::class => BrandProfileRepository::class,
        InfluencerProfileRepositoryInterface::class => InfluencerProfileRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        CampaignRepositoryInterface::class => CampaignRepository::class,
        CampaignApplicationRepositoryInterface::class => CampaignApplicationRepository::class,
        FileStorageInterface::class => LocalFileStorage::class,
        CollaborationRepositoryInterface::class => CollaborationRepository::class,
    ];
}