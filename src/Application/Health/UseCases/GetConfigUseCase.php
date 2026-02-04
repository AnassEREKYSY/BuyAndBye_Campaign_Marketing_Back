<?php

declare(strict_types=1);

namespace Src\Application\Health\UseCases;

use Src\Application\Health\DTOs\ConfigResponse;

class GetConfigUseCase
{
    public function execute(string $environment): ConfigResponse
    {
        return new ConfigResponse(
            apiVersion: 'v1',
            environment: $environment
        );
    }
}
