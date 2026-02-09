<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }
}