<?php

namespace App\Models;

use App\Enums\ConversationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Conversation extends Model
{
    use HasUuids;

    protected $table = 'conversations';

    protected $fillable = [
        'campaign_id',
        'campaign_application_id',
        'brand_user_id',
        'influencer_user_id',
        'status',
        'closed_at',
        'closed_reason',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'status' => ConversationStatus::class,
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function campaignApplication(): BelongsTo
    {
        return $this->belongsTo(CampaignApplication::class, 'campaign_application_id');
    }

    public function brandUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brand_user_id');
    }

    public function influencerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'influencer_user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class, 'conversation_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function isParticipant(string $userId): bool
    {
        return $this->brand_user_id === $userId || $this->influencer_user_id === $userId;
    }
}