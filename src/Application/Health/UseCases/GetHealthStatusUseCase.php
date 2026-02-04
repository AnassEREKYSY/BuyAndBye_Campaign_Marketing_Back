<?php

declare(strict_types=1);

namespace Src\Application\Health\UseCases;

use DateTimeImmutable;
use Src\Application\Health\DTOs\HealthStatusResponse;

class GetHealthStatusUseCase
{
    public function execute(): HealthStatusResponse
    {
        return new HealthStatusResponse(
            status: 'ok',
            timestamp: (new DateTimeImmutable())->format(DATE_ATOM)
        );
    }
}
