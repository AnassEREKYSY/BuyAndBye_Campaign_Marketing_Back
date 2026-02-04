<?php

declare(strict_types=1);

namespace Src\Application\Health\DTOs;

class HealthStatusResponse
{
    public function __construct(
        public string $status,
        public string $timestamp
    ) {
    }
}
