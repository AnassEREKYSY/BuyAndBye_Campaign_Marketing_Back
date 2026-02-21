<?php

declare(strict_types=1);

namespace App\Application\UseCases\Tracking;

use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Domain\Contracts\TrackingLinkRepositoryInterface;
use Illuminate\Support\Str;
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

        $ua = $userAgent ? trim($userAgent) : '';
        $isBot = $ua === '' || Str::contains(Str::lower($ua), ['bot', 'spider', 'crawl']);

        $uniqueKey = null;
        if (! $isBot) {
            $bucket = now()->format('Y-m-d');
            $uniqueKey = hash('sha256', ($ip ?? '') . '|' . $ua . '|' . $bucket);
        }

        $collab = $link->collaboration;

        $this->clicks->create([
            'tracking_link_id' => $link->id,
            'campaign_id' => $collab->campaign_id,
            'influencer_id' => $collab->influencer_id,
            'ip' => $ip,
            'user_agent' => $ua ?: null,
            'referrer' => $referrer,
            'unique_key' => $uniqueKey,
        ]);

        return $link->destination_url;
    }
}