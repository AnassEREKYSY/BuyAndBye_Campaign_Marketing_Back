<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $seller_id
 * @property string $title
 * @property string|null $description
 * @property string|null $category_id
 * @property string|null $condition
 * @property string|null $status
 * @property string $price
 * @property string|null $currency
 * @property string|null $compare_at_price
 * @property int $stock_quantity
 * @property array|null $images
 * @property array|null $tags
 * @property string|null $weight_kg
 * @property string|null $sku
 * @property bool $is_digital
 * @property bool $allow_returns
 * @property int $return_days
 * @property bool $is_featured
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'seller_id',
        'title',
        'description',
        'category_id',
        'condition',
        'status',
        'price',
        'currency',
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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'images' => 'array',
        'tags' => 'array',
        'weight_kg' => 'decimal:2',
        'is_digital' => 'boolean',
        'allow_returns' => 'boolean',
        'return_days' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (! $product->id) {
                $product->id = (string) Str::uuid();
            }
        });
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
