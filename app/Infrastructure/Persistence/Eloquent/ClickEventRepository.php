<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\ClickEventRepositoryInterface;
use App\Models\ClickEvent;

class ClickEventRepository implements ClickEventRepositoryInterface
{
    public function create(array $data): ClickEvent
    {
        return ClickEvent::create($data);
    }
}