<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CampaignApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'campaign_applications';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'campaign_id',
        'influencer_id',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => ApplicationStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $application) {
            if (! $application->id) {
                $application->id = (string) Str::uuid();
            }
        });
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function influencer()
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }
}