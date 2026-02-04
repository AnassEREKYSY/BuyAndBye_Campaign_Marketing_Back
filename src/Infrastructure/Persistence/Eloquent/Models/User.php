<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasUuids;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'users';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'password',
        'display_name',
        'phone_number',
        'birth_date',
        'gender',
        'role',
        'status',
        'photo_url',
        'last_login_at',
        'is_email_verified',
        'is_phone_verified',
        'locale',
        'country_code',
        'profile_completed_at',
        'profile_skipped',
        'following_seller_ids',
        'blocked_user_ids',
        'buyer_categories',
        'buyer_interests',
        'payment_methods',
        'is_deleted',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'profile_completed_at' => 'datetime',
        'profile_skipped' => 'boolean',
        'is_email_verified' => 'boolean',
        'is_phone_verified' => 'boolean',
        'following_seller_ids' => 'array',
        'blocked_user_ids' => 'array',
        'buyer_categories' => 'array',
        'buyer_interests' => 'array',
        'payment_methods' => 'array',
        'is_deleted' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class, 'user_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }
}
