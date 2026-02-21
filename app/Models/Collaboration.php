<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CollaborationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Collaboration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'collaborations';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'campaign_id',
        'brand_id',
        'influencer_id',
        'accepted_at',
        'status',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'status' => CollaborationStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $collaboration) {
            if (! $collaboration->id) {
                $collaboration->id = (string) Str::uuid();
            }
        });
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function brand()
    {
        return $this->belongsTo(User::class, 'brand_id');
    }

    public function influencer()
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }
    
    public function trackingLink()
    {
        return $this->hasOne(\App\Models\TrackingLink::class, 'collaboration_id');
    }

    public function promoCode()
    {
        return $this->hasOne(\App\Models\PromoCode::class, 'collaboration_id');
    }
}