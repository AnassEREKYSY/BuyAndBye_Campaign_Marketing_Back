<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, string $conversationId) {
    $c = Conversation::query()->find($conversationId);
    if (! $c) return false;
    return $c->isParticipant((string) $user->id);
});