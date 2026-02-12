<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'phone_number',
        'birth_date',
        'gender',
        'country_code',
        'locale',
        'buyer_categories',
        'buyer_interests',
        'payment_methods',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'buyer_categories' => 'array',
        'buyer_interests' => 'array',
        'payment_methods' => 'array',
    ];
}
