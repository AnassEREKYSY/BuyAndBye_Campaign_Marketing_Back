<?php

declare(strict_types=1);

namespace Src\Infrastructure\Services;

use Illuminate\Support\Facades\DB;
use Src\Domain\Shared\Services\TransactionManagerInterface;

class DatabaseTransactionManager implements TransactionManagerInterface
{
    public function run(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
