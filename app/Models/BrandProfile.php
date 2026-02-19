<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandProfile extends Model
{
    protected $table = 'brand_profiles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'brand_name',
        'website_url',
        'industry',
        'contact_email',
        'contact_phone',
        'description',
        'logo_url',
    ];
}