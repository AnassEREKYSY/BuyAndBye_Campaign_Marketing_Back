<?php

declare(strict_types=1);

namespace App\Application\UseCases\Users;

use App\Domain\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersUseCase
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function execute(int $page, int $size, ?string $role, ?string $status): LengthAwarePaginator
    {
        return $this->users->paginate($page, $size, $role, $status);
    }
}