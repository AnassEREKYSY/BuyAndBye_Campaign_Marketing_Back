<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClickEvent extends Model
{
    use HasFactory;

    protected $table = 'click_events';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tracking_link_id',
        'campaign_id',
        'influencer_id',
        'ip',
        'user_agent',
        'referrer',
        'unique_key',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $click) {
            if (! $click->id) {
                $click->id = (string) Str::uuid();
            }
        });
    }

    public function trackingLink()
    {
        return $this->belongsTo(TrackingLink::class, 'tracking_link_id');
    }
}