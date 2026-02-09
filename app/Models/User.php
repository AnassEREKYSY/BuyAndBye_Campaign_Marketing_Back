<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

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
            if (! $user->id) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }
}
