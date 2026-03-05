<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Dtos\Messaging\SendMessageDTO;
use App\Application\UseCases\Messaging\GetConversationUseCase;
use App\Application\UseCases\Messaging\ListConversationsUseCase;
use App\Application\UseCases\Messaging\ListMessagesUseCase;
use App\Application\UseCases\Messaging\MarkConversationReadUseCase;
use App\Application\UseCases\Messaging\SendMessageUseCase;
use App\Domain\Contracts\ConversationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Messaging - Conversations", description="Real-time messaging between Brand and Influencer")
 */
class ConversationController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/conversations",
     *     tags={"Messaging - Conversations"},
     *     summary="List my conversations",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated conversations",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Conversation")),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(ListConversationsUseCase $useCase): JsonResponse
    {
        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);

        $result = $useCase->execute((string) $this->user()->id, $page, $size);

        return response()->json([
            'data' => ConversationResource::collection($result['data']),
            'meta' => $result['meta'],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/conversations/unread-count",
     *     tags={"Messaging - Conversations"},
     *     summary="Get unread messages total count",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Unread count",
     *         @OA\JsonContent(
     *             @OA\Property(property="unread", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function unreadCount(ConversationRepositoryInterface $conversations): JsonResponse
    {
        $count = $conversations->unreadCountForUser((string) $this->user()->id);

        return response()->json([
            'unread' => (int) $count,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/conversations/{id}",
     *     tags={"Messaging - Conversations"},
     *     summary="Get a conversation with its messages",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Conversation",
     *         @OA\JsonContent(ref="#/components/schemas/Conversation")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id, GetConversationUseCase $useCase)
    {
        $conversation = $useCase->execute((string) $this->user()->id, $id);

        if (! $conversation) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return new ConversationResource($conversation);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/conversations/{id}/messages",
     *     tags={"Messaging - Conversations"},
     *     summary="List messages of a conversation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=50)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated messages",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Message")),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function messages(string $id, ListMessagesUseCase $useCase): JsonResponse
    {
        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 50);

        $result = $useCase->execute((string) $this->user()->id, $id, $page, $size);

        return response()->json([
            'data' => MessageResource::collection($result['data']),
            'meta' => $result['meta'],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/conversations/{id}/messages",
     *     tags={"Messaging - Conversations"},
     *     summary="Send a message in a conversation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"body"},
     *             @OA\Property(property="body", type="string", example="Hello, let's discuss the collaboration.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message created",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=409, description="Conversation closed")
     * )
     */
    public function send(string $id, SendMessageRequest $request, SendMessageUseCase $useCase)
    {
        $dto = new SendMessageDTO(
            conversationId: $id,
            body: (string) $request->input('body')
        );

        try {
            $message = $useCase->execute((string) $this->user()->id, $dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Conflict',
                'errors' => $e->errors(),
            ], 409);
        }

        if (! $message) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return (new MessageResource($message))->response()->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/conversations/{id}/read",
     *     tags={"Messaging - Conversations"},
     *     summary="Mark a conversation as read (updates last_read_at)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(@OA\Property(property="ok", type="boolean", example=true))
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function markRead(string $id, MarkConversationReadUseCase $useCase): JsonResponse
    {
        $ok = $useCase->execute((string) $this->user()->id, $id);

        if (! $ok) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json(['ok' => true]);
    }
}