<?php

namespace Src\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Shared\Enums\AccountStatus;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'email',
        'password',
        'display_name',
        'photo_url',
        'role',
        'status',
        'profile_completed',
        'profile_skipped',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'role' => UserRole::class,
        'status' => AccountStatus::class,
        'profile_completed' => 'boolean',
        'profile_skipped' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (!$user->id) {
                $user->id = (string) Str::uuid();
            }
        });
    }
}
