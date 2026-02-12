<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\ProductRepository;
use Illuminate\Support\ServiceProvider;
use App\Domain\Contracts\UserProfileRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\UserProfileRepository;
use App\Domain\Contracts\SellerProfileRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\SellerProfileRepository;
use App\Domain\Contracts\FileStorageInterface;
use App\Infrastructure\Storage\LocalFileStorage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            UserProfileRepositoryInterface::class,
            UserProfileRepository::class
        );
        $this->app->bind(
            SellerProfileRepositoryInterface::class,
            SellerProfileRepository::class
        );
        $this->app->bind(
            FileStorageInterface::class,
            LocalFileStorage::class
        );
    }

    public function boot(): void
    {
        //
    }
}
