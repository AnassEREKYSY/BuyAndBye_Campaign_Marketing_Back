<?php

namespace App\Application\Dtos\Messaging;

class SendMessageDTO
{
    public function __construct(
        public string $conversationId,
        public string $body
    ) {}
}