<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Domain\Contracts\SellerProfileRepositoryInterface;
use App\Domain\Contracts\UserProfileRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\ProductRepository;
use App\Infrastructure\Persistence\Eloquent\SellerProfileRepository;
use App\Infrastructure\Persistence\Eloquent\UserProfileRepository;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use App\Infrastructure\Storage\LocalFileStorage;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $singletons = [
        UserRepositoryInterface::class => UserRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        UserProfileRepositoryInterface::class => UserProfileRepository::class,
        SellerProfileRepositoryInterface::class => SellerProfileRepository::class,
        FileStorageInterface::class => LocalFileStorage::class,
    ];
}
