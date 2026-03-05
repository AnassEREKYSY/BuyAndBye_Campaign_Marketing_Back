<?php

namespace App\Application\UseCases\Messaging;

use App\Domain\Contracts\ConversationRepositoryInterface;
use App\Domain\Contracts\MessageRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class ListMessagesUseCase
{
    public function __construct(
        private ConversationRepositoryInterface $conversations,
        private MessageRepositoryInterface $messages
    ) {}

    public function execute(string $userId, string $conversationId, int $page = 1, int $size = 50): array
    {
        $conversation = $this->conversations->findById($conversationId);

        if (!$conversation) {
            return ['data' => [], 'meta' => null];
        }

        if (!$conversation->isParticipant($userId)) {
            throw new AuthorizationException('Forbidden');
        }

        return $this->messages->listByConversation($conversationId, $page, $size);
    }
}