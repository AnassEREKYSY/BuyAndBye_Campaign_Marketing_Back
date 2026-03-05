<?php

namespace App\Application\UseCases\Messaging;

use App\Domain\Contracts\ConversationRepositoryInterface;

class ListConversationsUseCase
{
    public function __construct(private ConversationRepositoryInterface $conversations) {}

    public function execute(string $userId, int $page = 1, int $size = 20): array
    {
        return $this->conversations->listForUser($userId, $page, $size);
    }
}