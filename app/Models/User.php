<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'email',
        'password',
        'display_name',
        'photo_url',
        'role',
        'status',
        'profile_completed',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'role' => UserRole::class,
        'status' => AccountStatus::class,
        'profile_completed' => 'boolean',
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (! $user->id) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    public function brandProfile(): HasOne
    {
        return $this->hasOne(BrandProfile::class, 'user_id');
    }

    public function influencerProfile(): HasOne
    {
        return $this->hasOne(InfluencerProfile::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'brand_id');
    }

    public function campaigns()
    {
        return $this->hasMany(\App\Models\Campaign::class, 'brand_id');
    }

    public function campaignApplications()
    {
        return $this->hasMany(\App\Models\CampaignApplication::class, 'influencer_id');
    }
}