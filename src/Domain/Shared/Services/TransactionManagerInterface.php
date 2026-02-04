<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Services;

interface TransactionManagerInterface
{
    public function run(callable $callback): mixed;
}
