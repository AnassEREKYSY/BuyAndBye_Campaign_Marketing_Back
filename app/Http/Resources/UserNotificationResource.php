<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'type' => (string) $this->type,
            'title' => (string) $this->title,
            'body' => $this->body,
            'data' => $this->data,

            'entity' => [
                'type' => $this->entity_type,
                'id' => $this->entity_id,
            ],

            'actor' => $this->actor_id ? [
                'id' => $this->actor_id,
            ] : null,

            'read_at' => $this->read_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}