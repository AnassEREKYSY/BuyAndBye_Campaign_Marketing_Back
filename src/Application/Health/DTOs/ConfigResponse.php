<?php

declare(strict_types=1);

namespace Src\Application\Health\DTOs;

class ConfigResponse
{
    public function __construct(
        public string $apiVersion,
        public string $environment
    ) {
    }
}
