<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CampaignPayoutTier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'campaign_payout_tiers';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'campaign_id',
        'metric',
        'from_value',
        'to_value',
        'payout_amount',
        'currency',
    ];

    protected $casts = [
        'from_value' => 'integer',
        'to_value' => 'integer',
        'payout_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $tier) {
            if (! $tier->id) {
                $tier->id = (string) Str::uuid();
            }
        });
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}