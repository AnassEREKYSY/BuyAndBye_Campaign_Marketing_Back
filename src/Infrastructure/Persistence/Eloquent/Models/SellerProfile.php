<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerProfile extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'seller_profiles';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'store_name',
        'store_description',
        'store_banner_url',
        'verification_status',
        'rating_average',
        'rating_count',
        'vat_number',
        'company_name',
        'support_email',
        'support_phone',
        'category_tags',
        'is_pro_seller',
        'is_deleted',
    ];

    protected $casts = [
        'rating_average' => 'float',
        'rating_count' => 'integer',
        'category_tags' => 'array',
        'is_pro_seller' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
