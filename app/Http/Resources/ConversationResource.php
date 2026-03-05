<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\ConversationStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray($request): array
    {
        $participants = $this->relationLoaded('participants') ? $this->participants : null;

        $status = $this->status;
        if ($status instanceof ConversationStatus) {
            $status = $status->value;
        } elseif (is_object($status) && property_exists($status, 'value')) {
            $status = $status->value;
        } else {
            $status = (string) $status;
        }

        return [
            'id' => (string) $this->id,
            'campaign_id' => (string) $this->campaign_id,
            'campaign_application_id' => (string) $this->campaign_application_id,
            'brand_user_id' => (string) $this->brand_user_id,
            'influencer_user_id' => (string) $this->influencer_user_id,
            'status' => $status,
            'closed_at' => optional($this->closed_at)->toISOString(),
            'closed_reason' => $this->closed_reason,

            'messages_count' => $this->when(isset($this->messages_count), (int) $this->messages_count),
            'unread_count' => $this->when(isset($this->unread_count), (int) $this->unread_count),
            'last_message_at' => $this->when(isset($this->last_message_at), optional($this->last_message_at)->toISOString()),
            'last_message_body' => $this->when(isset($this->last_message_body), (string) $this->last_message_body),

            'campaign' => $this->whenLoaded('campaign', function () {
                return [
                    'id' => (string) $this->campaign->id,
                    'title' => $this->campaign->title,
                ];
            }),

            'participants' => $participants ? $participants->map(function ($p) {
                return [
                    'user_id' => (string) $p->user_id,
                    'last_read_at' => optional($p->last_read_at)->toISOString(),
                ];
            })->values() : null,

            'messages' => $this->whenLoaded('messages', function () {
                return MessageResource::collection($this->messages);
            }),

            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}