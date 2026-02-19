<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User
    {
        return User::withTrashed()->where('id', $id)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function save(User $user): void
    {
        $user->save();
    }

    public function update(User $user, array $data): void
    {
        $user->update($data);
    }

    public function paginate(int $page, int $size, ?string $role = null, ?string $status = null): LengthAwarePaginator
    {
        $q = User::query()->withTrashed();

        if ($role) {
            $q->where('role', $role);
        }
        if ($status) {
            $q->where('status', $status);
        }

        return $q->orderByDesc('created_at')->paginate(perPage: $size, page: $page);
    }

    public function softDelete(User $user): void
    {
        $user->delete();
    }

    public function restore(string $id): ?User
    {
        $user = User::withTrashed()->where('id', $id)->first();
        if (! $user) return null;

        $user->restore();
        return $user;
    }
}