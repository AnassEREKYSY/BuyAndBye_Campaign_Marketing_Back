<?php

namespace App\Domain\Contracts;

use App\Models\Message;

interface MessageRepositoryInterface
{
    public function create(string $conversationId, string $senderId, string $body): Message;

    public function listByConversation(string $conversationId, int $page = 1, int $size = 50): array;
}