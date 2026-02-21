<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TrackingLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tracking_links';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'collaboration_id',
        'code',
        'destination_url',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $link) {
            if (! $link->id) {
                $link->id = (string) Str::uuid();
            }
        });
    }

    public function collaboration()
    {
        return $this->belongsTo(Collaboration::class, 'collaboration_id');
    }

    public function clicks()
    {
        return $this->hasMany(ClickEvent::class, 'tracking_link_id');
    }
}