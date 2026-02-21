<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CollaborationPayout extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'collaboration_payouts';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'collaboration_id',
        'period_start',
        'period_end',
        'clicks_total',
        'clicks_unique',
        'tier_id',
        'amount',
        'currency',
        'status',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'clicks_total' => 'integer',
        'clicks_unique' => 'integer',
        'amount' => 'decimal:2',
        'status' => PayoutStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $payout) {
            if (! $payout->id) {
                $payout->id = (string) Str::uuid();
            }
        });
    }

    public function collaboration()
    {
        return $this->belongsTo(Collaboration::class, 'collaboration_id');
    }

    public function tier()
    {
        return $this->belongsTo(CampaignPayoutTier::class, 'tier_id');
    }
}