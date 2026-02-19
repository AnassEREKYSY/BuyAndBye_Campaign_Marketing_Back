<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Contracts\BrandProfileRepositoryInterface;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\InfluencerProfileRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\BrandProfileRepository;
use App\Infrastructure\Persistence\Eloquent\InfluencerProfileRepository;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use App\Infrastructure\Storage\LocalFileStorage;
use Illuminate\Support\ServiceProvider;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\ProductRepository;
use App\Infrastructure\Persistence\Eloquent\CampaignRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $singletons = [
        UserRepositoryInterface::class => UserRepository::class,
        BrandProfileRepositoryInterface::class => BrandProfileRepository::class,
        InfluencerProfileRepositoryInterface::class => InfluencerProfileRepository::class,
        FileStorageInterface::class => LocalFileStorage::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        CampaignRepositoryInterface::class => CampaignRepository::class,
    ];
}