<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfluencerProfile extends Model
{
    protected $table = 'influencer_profiles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'niche',
        'instagram_url',
        'tiktok_url',
        'youtube_url',
        'followers_instagram',
        'followers_tiktok',
        'followers_youtube',
        'avg_engagement_rate',
        'country_code',
        'language',
        'media_kit_url',
    ];

    protected $casts = [
        'followers_instagram' => 'integer',
        'followers_tiktok' => 'integer',
        'followers_youtube' => 'integer',
        'avg_engagement_rate' => 'float',
    ];
}