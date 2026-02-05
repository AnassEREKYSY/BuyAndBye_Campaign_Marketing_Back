<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Repositories;

use Src\Domain\Users\Entities\User as UserEntity;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Shared\Enums\AccountStatus;
use Src\Infrastructure\Persistence\Eloquent\Models\User as UserModel;

final class UserRepository implements UserRepositoryInterface
{
    public function create(array $attributes): UserEntity
    {
        $model = UserModel::query()->create($attributes);

        return $this->toDomain($model);
    }

    public function update(string $id, array $attributes): UserEntity
    {
        $model = UserModel::query()->findOrFail($id);
        $model->update($attributes);

        return $this->toDomain($model);
    }

    public function findById(string $id): ?UserEntity
    {
        $model = UserModel::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $model = UserModel::query()->where('email', $email)->first();

        return $model ? $this->toDomain($model) : null;
    }

    private function toDomain(UserModel $model): UserEntity
    {
        return new UserEntity(
            id: $model->id,
            email: $model->email,
            password: $model->password,
            displayName: $model->display_name,
            role: $model->role,
            status: $model->status,
            photoUrl: $model->photo_url,
            profileCompleted: (bool) ($model->profile_completed ?? false),
            profileSkipped: (bool) ($model->profile_skipped ?? false),
            emailVerifiedAt: $model->email_verified_at?->toDateTimeImmutable(),
            createdAt: $model->created_at?->toDateTimeImmutable(),
            updatedAt: $model->updated_at?->toDateTimeImmutable(),
        );
    }
}
