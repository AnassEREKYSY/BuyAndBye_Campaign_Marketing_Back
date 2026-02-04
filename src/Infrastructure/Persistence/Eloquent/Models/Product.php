<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'products';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'seller_id',
        'title',
        'description',
        'category_id',
        'condition',
        'status',
        'price',
        'compare_at_price',
        'stock_quantity',
        'images',
        'tags',
        'weight_kg',
        'sku',
        'is_digital',
        'allow_returns',
        'return_days',
        'is_featured',
        'published_at',
        'is_deleted',
    ];

    protected $casts = [
        'price' => 'float',
        'compare_at_price' => 'float',
        'stock_quantity' => 'integer',
        'images' => 'array',
        'tags' => 'array',
        'weight_kg' => 'float',
        'is_digital' => 'boolean',
        'allow_returns' => 'boolean',
        'return_days' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
