<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Repositories;

use DateTimeImmutable;
use Src\Domain\Users\Entities\User as UserEntity;
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Infrastructure\Persistence\Eloquent\Models\User as UserModel;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $attributes): UserEntity
    {
        $model = UserModel::query()->create($attributes);

        return $this->toDomain($model);
    }

    public function update(string $id, array $attributes): UserEntity
    {
        $model = UserModel::query()->findOrFail($id);
        $model->fill($attributes);
        $model->save();

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
        $birthDate = $model->birth_date?->toDateTimeImmutable();
        $lastLoginAt = $model->last_login_at?->toDateTimeImmutable();
        $profileCompletedAt = $model->profile_completed_at?->toDateTimeImmutable();

        return new UserEntity(
            id: $model->id,
            email: $model->email,
            password: $model->password,
            displayName: $model->display_name,
            role: (int) $model->role,
            status: $model->status,
            photoUrl: $model->photo_url,
            phoneNumber: $model->phone_number,
            birthDate: $birthDate instanceof DateTimeImmutable ? $birthDate : null,
            gender: $model->gender,
            isEmailVerified: (bool) $model->is_email_verified,
            isPhoneVerified: (bool) $model->is_phone_verified,
            locale: $model->locale,
            countryCode: $model->country_code,
            profileCompletedAt: $profileCompletedAt instanceof DateTimeImmutable ? $profileCompletedAt : null,
            profileSkipped: (bool) $model->profile_skipped,
            followingSellerIds: $model->following_seller_ids ?? [],
            blockedUserIds: $model->blocked_user_ids ?? [],
            buyerCategories: $model->buyer_categories,
            buyerInterests: $model->buyer_interests,
            paymentMethods: $model->payment_methods,
            lastLoginAt: $lastLoginAt instanceof DateTimeImmutable ? $lastLoginAt : null
        );
    }
}
