<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Auth extends Model
{
    use HasFactory;

    protected $table = 'auths';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'provider',
        'provider_id',
        'access_token',
        'refresh_token',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $auth) {
            if (! $auth->id) {
                $auth->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
