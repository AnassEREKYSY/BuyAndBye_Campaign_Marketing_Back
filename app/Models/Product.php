<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'brand_id',
        'name',
        'description',
        'price',
        'currency',
        'landing_url',
        'status',
        'images',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'images' => 'array',
        'status' => ProductStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (! $product->id) {
                $product->id = (string) Str::uuid();
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(User::class, 'brand_id');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'product_id');
    }
}