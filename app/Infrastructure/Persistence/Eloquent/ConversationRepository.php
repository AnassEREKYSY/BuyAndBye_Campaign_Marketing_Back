<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\ConversationRepositoryInterface;
use App\Enums\ConversationStatus;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function findById(string $id): ?Conversation
    {
        return Conversation::query()
            ->with([
                'campaign:id,title',
                'campaignApplication:id,campaign_id,influencer_id,status,created_at',
                'brandUser:id,email',
                'influencerUser:id,email',
                'participants',
            ])
            ->find($id);
    }

    public function findByApplicationId(string $campaignApplicationId): ?Conversation
    {
        return Conversation::query()->where('campaign_application_id', $campaignApplicationId)->first();
    }

    public function createForShortlistedApplication(
        string $campaignId,
        string $campaignApplicationId,
        string $brandUserId,
        string $influencerUserId
    ): Conversation {
        $existing = Conversation::query()->where('campaign_application_id', $campaignApplicationId)->first();
        if ($existing) {
            return $existing;
        }

        $conversation = Conversation::query()->create([
            'campaign_id' => $campaignId,
            'campaign_application_id' => $campaignApplicationId,
            'brand_user_id' => $brandUserId,
            'influencer_user_id' => $influencerUserId,
            'status' => ConversationStatus::Open->value,
            'closed_at' => null,
            'closed_reason' => null,
        ]);

        ConversationParticipant::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $brandUserId,
            'last_read_at' => Carbon::now(),
        ]);

        ConversationParticipant::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $influencerUserId,
            'last_read_at' => Carbon::now(),
        ]);

        return $conversation;
    }

    public function closeConversation(string $conversationId, string $reason): Conversation
    {
        $conversation = Conversation::query()->findOrFail($conversationId);

        $conversation->status = ConversationStatus::Closed;
        $conversation->closed_at = Carbon::now();
        $conversation->closed_reason = $reason;
        $conversation->save();

        return $conversation;
    }

    public function listForUser(string $userId, int $page = 1, int $size = 20): array
    {
        $lastMessageAtSub = Message::query()
            ->selectRaw('MAX(created_at)')
            ->whereColumn('conversation_id', 'conversations.id');

        $lastMessageBodySub = Message::query()
            ->select('body')
            ->whereColumn('conversation_id', 'conversations.id')
            ->orderByDesc('created_at')
            ->limit(1);

        $unreadCountSub = Message::query()
            ->selectRaw('COUNT(*)')
            ->whereColumn('conversation_id', 'conversations.id')
            ->where('sender_id', '!=', $userId)
            ->whereRaw(
                'created_at > COALESCE((SELECT last_read_at FROM conversation_participants cp WHERE cp.conversation_id = conversations.id AND cp.user_id = ? LIMIT 1), ?)',
                [$userId, '1970-01-01 00:00:00']
            );

        $q = Conversation::query()
            ->where(function ($x) use ($userId) {
                $x->where('brand_user_id', $userId)->orWhere('influencer_user_id', $userId);
            })
            ->with(['campaign:id,title'])
            ->withCount('messages')
            ->select('conversations.*')
            ->selectSub($lastMessageAtSub, 'last_message_at')
            ->selectSub($lastMessageBodySub, 'last_message_body')
            ->selectSub($unreadCountSub, 'unread_count')
            ->orderByRaw('COALESCE((select MAX(created_at) from messages where conversation_id = conversations.id), conversations.updated_at) desc');

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

    public function unreadCountForUser(string $userId): int
    {
        $rows = DB::table('conversations')
            ->join('conversation_participants as cp', function ($join) use ($userId) {
                $join->on('cp.conversation_id', '=', 'conversations.id')
                    ->where('cp.user_id', '=', $userId);
            })
            ->join('messages as m', function ($join) use ($userId) {
                $join->on('m.conversation_id', '=', 'conversations.id')
                    ->where('m.sender_id', '!=', $userId);
            })
            ->where(function ($x) use ($userId) {
                $x->where('conversations.brand_user_id', $userId)->orWhere('conversations.influencer_user_id', $userId);
            })
            ->whereRaw('m.created_at > COALESCE(cp.last_read_at, ?)', ['1970-01-01 00:00:00'])
            ->count();

        return (int) $rows;
    }
}