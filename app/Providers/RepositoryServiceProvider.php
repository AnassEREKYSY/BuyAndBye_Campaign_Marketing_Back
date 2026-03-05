<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Contracts\BrandProfileRepositoryInterface;
use App\Domain\Contracts\CampaignApplicationRepositoryInterface;
use App\Domain\Contracts\CampaignRepositoryInterface;
use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\CollaborationPayoutRepositoryInterface;
use App\Domain\Contracts\CollaborationRepositoryInterface;
use App\Domain\Contracts\ConversationRepositoryInterface;
use App\Domain\Contracts\FileStorageInterface;
use App\Domain\Contracts\InfluencerProfileRepositoryInterface;
use App\Domain\Contracts\MessageRepositoryInterface;
use App\Domain\Contracts\NotificationServiceInterface;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Contracts\TrackingLinkRepositoryInterface;
use App\Domain\Contracts\UserNotificationRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\BrandProfileRepository;
use App\Infrastructure\Persistence\Eloquent\CampaignApplicationRepository;
use App\Infrastructure\Persistence\Eloquent\CampaignPayoutTierRepository;
use App\Infrastructure\Persistence\Eloquent\CampaignRepository;
use App\Infrastructure\Persistence\Eloquent\ClickEventRepository;
use App\Infrastructure\Persistence\Eloquent\CollaborationPayoutRepository;
use App\Infrastructure\Persistence\Eloquent\CollaborationRepository;
use App\Infrastructure\Persistence\Eloquent\ConversationRepository;
use App\Infrastructure\Persistence\Eloquent\InfluencerProfileRepository;
use App\Infrastructure\Persistence\Eloquent\MessageRepository;
use App\Infrastructure\Persistence\Eloquent\ProductRepository;
use App\Infrastructure\Persistence\Eloquent\PromoCodeRepository;
use App\Infrastructure\Persistence\Eloquent\TrackingLinkRepository;
use App\Infrastructure\Persistence\Eloquent\UserNotificationRepository;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use App\Infrastructure\Storage\LocalFileStorage;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\ServiceProvider;
use App\Domain\Contracts\CampaignPayoutTierRepositoryInterface;

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
        ClickEventRepositoryInterface::class => ClickEventRepository::class,
        TrackingLinkRepositoryInterface::class => TrackingLinkRepository::class,
        PromoCodeRepositoryInterface::class => PromoCodeRepository::class,
        CampaignPayoutTierRepositoryInterface::class => CampaignPayoutTierRepository::class,
        CollaborationPayoutRepositoryInterface::class => CollaborationPayoutRepository::class,
        UserNotificationRepositoryInterface::class => UserNotificationRepository::class,
        NotificationServiceInterface::class => NotificationService::class,

        ConversationRepositoryInterface::class => ConversationRepository::class,
        MessageRepositoryInterface::class => MessageRepository::class,
    ];
}