<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CommissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'campaigns';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'brand_id',
        'product_id',
        'title',
        'objective',
        'commission_type',
        'commission_value',
        'budget',
        'start_at',
        'end_at',
        'status',
    ];

    protected $casts = [
        'commission_value' => 'decimal:2',
        'budget' => 'decimal:2',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => CampaignStatus::class,
        'commission_type' => CommissionType::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $campaign) {
            if (! $campaign->id) {
                $campaign->id = (string) Str::uuid();
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(User::class, 'brand_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}