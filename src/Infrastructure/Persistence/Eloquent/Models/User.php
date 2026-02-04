<?php

namespace Src\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
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
        'role',
        'profile_completed',
        'profile_skipped',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'profile_completed' => 'boolean',
        'profile_skipped' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function auths(): HasMany
    {
        return $this->hasMany(Auth::class, 'user_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id', 'id');
    }
}