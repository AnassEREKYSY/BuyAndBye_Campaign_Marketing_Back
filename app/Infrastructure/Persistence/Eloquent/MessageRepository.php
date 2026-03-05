<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\MessageRepositoryInterface;
use App\Models\Message;

class MessageRepository implements MessageRepositoryInterface
{
    public function create(string $conversationId, string $senderId, string $body): Message
    {
        return Message::query()->create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'body' => $body,
        ]);
    }

    public function listByConversation(string $conversationId, int $page = 1, int $size = 50): array
    {
        $q = Message::query()
            ->where('conversation_id', $conversationId)
            ->orderByDesc('created_at');

        $p = $q->paginate($size, ['*'], 'page', $page);

        return [
            'data' => $p->items(),
            'meta' => [
                'current_page' => $p->currentPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
                'last_page' => $p->lastPage(),
                'next_page_url' => $p->nextPageUrl(),
                'prev_page_url' => $p->previousPageUrl(),
            ],
        ];
    }
}