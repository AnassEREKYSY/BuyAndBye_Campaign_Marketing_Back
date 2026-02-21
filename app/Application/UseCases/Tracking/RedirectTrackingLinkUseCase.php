<?php

declare(strict_types=1);

namespace App\Application\UseCases\Tracking;

use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\TrackingLinkRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectTrackingLinkUseCase
{
    public function __construct(
        private readonly TrackingLinkRepositoryInterface $trackingLinks,
        private readonly ClickEventRepositoryInterface $clicks,
    ) {}

    public function execute(string $code, ?string $ip, ?string $userAgent, ?string $referrer): string
    {
        $link = $this->trackingLinks->findByCode($code);

        if (! $link) {
            throw new NotFoundHttpException('Tracking link not found.');
        }

        $collab = $link->collaboration;

        $this->clicks->create([
            'tracking_link_id' => $link->id,
            'campaign_id' => $collab->campaign_id,
            'influencer_id' => $collab->influencer_id,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
        ]);

        return $link->destination_url;
    }
}