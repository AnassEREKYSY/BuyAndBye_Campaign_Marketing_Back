<?php

namespace App\Application\UseCases\Messaging;

use App\Application\Dtos\Messaging\SendMessageDTO;
use App\Domain\Contracts\ConversationRepositoryInterface;
use App\Domain\Contracts\MessageRepositoryInterface;
use App\Enums\ConversationStatus;
use App\Events\MessageSent;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class SendMessageUseCase
{
    public function __construct(
        private ConversationRepositoryInterface $conversations,
        private MessageRepositoryInterface $messages
    ) {}

    public function execute(string $userId, SendMessageDTO $dto)
    {
        $conversation = $this->conversations->findById($dto->conversationId);

        if (!$conversation) {
            return null;
        }

        if (!$conversation->isParticipant($userId)) {
            throw new AuthorizationException('Forbidden');
        }

        if ($conversation->status === ConversationStatus::Closed) {
            throw ValidationException::withMessages([
                'conversation' => ['Conversation is closed.'],
            ]);
        }

        $message = $this->messages->create($conversation->id, $userId, $dto->body);
        $message->load('sender:id,email');

        event(new MessageSent($conversation->id, $message));

        return $message;
    }
}