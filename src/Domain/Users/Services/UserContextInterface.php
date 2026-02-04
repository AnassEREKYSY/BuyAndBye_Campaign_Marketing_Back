<?php

declare(strict_types=1);

namespace Src\Domain\Users\Services;

interface UserContextInterface
{
    public function getUserId(): string;

    public function getUserRole(): int;
}
