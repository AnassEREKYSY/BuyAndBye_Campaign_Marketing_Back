<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    protected $table = 'seller_profiles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'store_name',
        'company_name',
        'vat_number',
        'support_email',
        'support_phone',
        'category_tags',
        'store_description',
        'store_banner_url',
    ];

    protected $casts = [
        'category_tags' => 'array',
    ];
}
