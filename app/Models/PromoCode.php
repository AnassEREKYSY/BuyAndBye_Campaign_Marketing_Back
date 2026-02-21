<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'promo_codes';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'collaboration_id',
        'code',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $promo) {
            if (! $promo->id) {
                $promo->id = (string) Str::uuid();
            }
        });
    }

    public function collaboration()
    {
        return $this->belongsTo(Collaboration::class, 'collaboration_id');
    }
}