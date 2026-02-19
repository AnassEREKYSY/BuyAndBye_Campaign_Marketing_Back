<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): void;

    public function update(User $user, array $data): void;

    public function paginate(int $page, int $size, ?string $role = null, ?string $status = null): LengthAwarePaginator;

    public function softDelete(User $user): void;

    public function restore(string $id): ?User;
}