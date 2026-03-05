<?php

namespace App\Application\UseCases\Messaging;

use App\Domain\Contracts\ConversationRepositoryInterface;
use App\Models\ConversationParticipant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;

class MarkConversationReadUseCase
{
    public function __construct(private ConversationRepositoryInterface $conversations) {}

    public function execute(string $userId, string $conversationId): bool
    {
        $conversation = $this->conversations->findById($conversationId);

        if (!$conversation) {
            return false;
        }

        if (!$conversation->isParticipant($userId)) {
            throw new AuthorizationException('Forbidden');
        }

        ConversationParticipant::query()
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['last_read_at' => Carbon::now()]);

        return true;
    }
}