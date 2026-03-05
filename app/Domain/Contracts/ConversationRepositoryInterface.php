<?php

namespace App\Domain\Contracts;

use App\Models\Conversation;

interface ConversationRepositoryInterface
{
    public function findById(string $id): ?Conversation;

    public function findByApplicationId(string $campaignApplicationId): ?Conversation;

    public function createForShortlistedApplication(
        string $campaignId,
        string $campaignApplicationId,
        string $brandUserId,
        string $influencerUserId
    ): Conversation;

    public function closeConversation(string $conversationId, string $reason): Conversation;

    public function listForUser(string $userId, int $page = 1, int $size = 20): array;

    public function unreadCountForUser(string $userId): int;
}