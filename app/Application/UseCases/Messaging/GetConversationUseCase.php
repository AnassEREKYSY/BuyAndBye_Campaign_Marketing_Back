<?php

namespace App\Application\UseCases\Messaging;

use App\Domain\Contracts\ConversationRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class GetConversationUseCase
{
    public function __construct(private ConversationRepositoryInterface $conversations) {}

    public function execute(string $userId, string $conversationId)
    {
        $conversation = $this->conversations->findById($conversationId);

        if (!$conversation) {
            return null;
        }

        if (!$conversation->isParticipant($userId)) {
            throw new AuthorizationException('Forbidden');
        }

        $conversation->load(['messages.sender:id,email']);

        return $conversation;
    }
}