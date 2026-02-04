<?php

declare(strict_types=1);

namespace Src\Infrastructure\Services;

use Google\Client as GoogleClient;
use Src\Domain\Auth\Services\GoogleAuthServiceInterface;
use Src\Domain\Shared\Exceptions\UnauthorizedException;

class GoogleAuthService implements GoogleAuthServiceInterface
{
    public function verifyIdToken(string $idToken): array
    {
        $client = new GoogleClient(['client_id' => config('services.google.client_id')]);
        $payload = $client->verifyIdToken($idToken);

        if (!is_array($payload) || empty($payload['email'])) {
            throw UnauthorizedException::create();
        }

        return [
            'email' => (string) $payload['email'],
            'name' => $payload['name'] ?? null,
            'photo_url' => $payload['picture'] ?? null,
        ];
    }
}
